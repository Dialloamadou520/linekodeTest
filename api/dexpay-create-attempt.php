<?php
// Headers CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuration DexpayAfrica
define('DEXPAY_API_KEY', getenv('DEXPAY_API_KEY') ?: 'pk_live_VOTRE_CLE_PUBLIQUE');
define('DEXPAY_BASE_URL', 'https://api.dexpay.africa/api/v1');

// Fonction pour créer une tentative de paiement
function createPaymentAttempt($reference, $data) {
    $url = DEXPAY_BASE_URL . '/checkout-sessions/' . $reference . '/attempts';
    
    $ch = curl_init($url);
    
    $payload = [
        'payment_method' => $data['payment_method'],
        'operator' => $data['operator'],
        'customer' => $data['customer'],
        'countryISO' => $data['countryISO']
    ];
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . DEXPAY_API_KEY,
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Log de débogage
    error_log("🔍 Payment Attempt URL: " . $url);
    error_log("🔍 Payment Attempt Payload: " . json_encode($payload));
    error_log("🔍 Payment Attempt Response Code: " . $httpCode);
    error_log("🔍 Payment Attempt Response: " . $response);
    
    if ($error) {
        error_log("❌ cURL Error: " . $error);
        return [
            'success' => false,
            'error' => 'Erreur de connexion: ' . $error
        ];
    }
    
    $result = json_decode($response, true);
    
    if ($httpCode === 200 || $httpCode === 201) {
        return [
            'success' => true,
            'status' => $httpCode,
            'data' => $result['data'] ?? $result
        ];
    } else {
        return [
            'success' => false,
            'status' => $httpCode,
            'error' => $result['message'] ?? 'Erreur lors de la création de la tentative',
            'details' => $result
        ];
    }
}

// Traiter la requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    error_log("📥 Create Attempt Input: " . $input);
    
    if (empty($input)) {
        echo json_encode([
            'success' => false,
            'error' => 'Aucune donnée reçue'
        ]);
        exit();
    }
    
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'success' => false,
            'error' => 'JSON invalide: ' . json_last_error_msg()
        ]);
        exit();
    }
    
    // Validation
    if (!isset($data['reference'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Référence de session manquante'
        ]);
        exit();
    }
    
    if (!isset($data['operator']) || !isset($data['customer'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Données de paiement incomplètes'
        ]);
        exit();
    }
    
    // Créer la tentative de paiement
    $reference = $data['reference'];
    unset($data['reference']); // Retirer la référence du payload
    
    $result = createPaymentAttempt($reference, $data);
    
    echo json_encode($result);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Méthode non autorisée'
    ]);
}

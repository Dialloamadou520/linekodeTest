<?php
// API DexpayAfrica Checkout pour Linekode
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
define('DEXPAY_SECRET_KEY', getenv('DEXPAY_SECRET_KEY') ?: 'sk_live_VOTRE_CLE_SECRETE');
define('DEXPAY_BASE_URL', 'https://api.dexpay.africa/api/v1');
// Mode simulation pour développement local
define('SIMULATION_MODE', false); // PRODUCTION - Paiements réels DexpayAfrica activés

// Fonction pour créer une session simulée
function createSimulatedSession($data) {
    $sessionId = 'sim_' . uniqid() . '_' . time();
    $reference = 'LINEKODE_SIM_' . uniqid() . '_' . time();
    
    // Simuler une réponse DexpayAfrica
    return [
        'success' => true,
        'session_id' => $sessionId,
        'reference' => $reference,
        'checkout_url' => $data['success_url'], // Rediriger directement vers success
        'data' => [
            'id' => $sessionId,
            'reference' => $reference,
            'amount' => $data['amount'],
            'currency' => 'XOF',
            'status' => 'pending',
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'],
            'description' => $data['description'],
            'created_at' => date('Y-m-d H:i:s'),
            'mode' => 'simulation'
        ]
    ];
}

// Fonction pour créer une session de paiement réelle
function createCheckoutSession($data) {
    $ch = curl_init(DEXPAY_BASE_URL . '/checkout-sessions');
    
    $payload = [
        'reference' => 'LINEKODE_' . uniqid() . '_' . time(),
        'item_name' => $data['description'] ?? 'Inscription Formation Linekode',
        'amount' => $data['amount'],
        'currency' => 'XOF',
        'countryISO' => 'SN',
        'webhook_url' => $data['webhook_url'] ?? 'https://linekode.com/api/dexpay-webhook.php',
        'success_url' => $data['success_url'] ?? 'https://linekode.com/payment-success.php',
        'failure_url' => $data['cancel_url'] ?? 'https://linekode.com/payment-cancelled.php',
        'custom_metadata' => [
            'inscription_id' => $data['metadata']['inscription_id'] ?? uniqid(),
            'source' => $data['metadata']['source'] ?? 'linekode_website',
            'customer_phone' => $data['customer_phone'] ?? '',
            'customer_email' => $data['customer_email'] ?? ''
        ]
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
    
    if ($error) {
        // En cas d'erreur de connexion
        error_log("❌ DexpayAfrica API cURL Error: " . $error);
        return [
            'success' => false,
            'error' => 'Erreur de connexion à l\'API DexpayAfrica: ' . $error
        ];
    }
    
    $result = json_decode($response, true);
    
    // Log de débogage détaillé
    error_log("🔍 DexpayAfrica API Response Code: " . $httpCode);
    error_log("🔍 DexpayAfrica API Response: " . json_encode($result));
    error_log("🔍 Payload Reference: " . $payload['reference']);
    
    if ($httpCode === 200 || $httpCode === 201) {
        // Extraire la référence de la réponse API ou utiliser celle du payload
        $apiReference = $result['data']['reference'] ?? $payload['reference'];
        
        $responseData = [
            'success' => true,
            'session_id' => $result['data']['id'] ?? uniqid('dexpay_'),
            'reference' => $apiReference,
            'checkout_url' => $result['data']['payment_url'] ?? null,
            'data' => $result['data'] ?? $result
        ];
        
        // Log de débogage de la réponse finale
        error_log("✅ Response Data: " . json_encode($responseData));
        error_log("✅ Reference returned: " . $apiReference);
        
        return $responseData;
    } else {
        // En cas d'erreur API, retourner l'erreur
        error_log("❌ DexpayAfrica API Response Error: " . json_encode($result));
        return [
            'success' => false,
            'error' => $result['message'] ?? 'Erreur API DexpayAfrica',
            'http_code' => $httpCode,
            'details' => $result
        ];
    }
}

// Traiter la requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mode debug ultra-détaillé
    $debug_info = [
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'not set',
        'http_accept' => $_SERVER['HTTP_ACCEPT'] ?? 'not set',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'not set'
    ];
    error_log("🔍 DEBUG Headers: " . json_encode($debug_info));
    
    $input = file_get_contents('php://input');
    
    // Log de l'input brut avec longueur
    error_log("📥 Input brut reçu (longueur: " . strlen($input) . "): " . substr($input, 0, 500));
    
    // Vérifier que l'input n'est pas vide
    if (empty($input)) {
        $error_response = [
            'success' => false,
            'error' => 'Aucune donnée reçue (body vide)',
            'debug' => $debug_info
        ];
        error_log("❌ Erreur: Body vide - " . json_encode($error_response));
        echo json_encode($error_response);
        exit();
    }
    
    $data = json_decode($input, true);
    
    // Vérifier que le JSON est valide
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("❌ Erreur JSON: " . json_last_error_msg());
        echo json_encode([
            'success' => false,
            'error' => 'JSON invalide: ' . json_last_error_msg(),
            'raw_input' => $input
        ]);
        exit();
    }
    
    // Vérifier que $data n'est pas null
    if ($data === null) {
        echo json_encode([
            'success' => false,
            'error' => 'Données null après décodage JSON',
            'raw_input' => $input
        ]);
        exit();
    }
    
    // Log des données décodées
    error_log("📥 Données décodées: " . json_encode($data));
    
    // Validation des données
    if (!isset($data['amount']) || !isset($data['customer_phone']) || !isset($data['customer_email'])) {
        error_log("❌ Validation échouée - Données manquantes");
        error_log("Amount: " . (isset($data['amount']) ? 'OK' : 'MANQUANT'));
        error_log("Phone: " . (isset($data['customer_phone']) ? 'OK' : 'MANQUANT'));
        error_log("Email: " . (isset($data['customer_email']) ? 'OK' : 'MANQUANT'));
        
        echo json_encode([
            'success' => false,
            'error' => 'Données manquantes (amount, customer_phone, customer_email requis)',
            'received_data' => $data,
            'missing_fields' => [
                'amount' => !isset($data['amount']),
                'customer_phone' => !isset($data['customer_phone']),
                'customer_email' => !isset($data['customer_email'])
            ]
        ]);
        exit();
    }
    
    // Créer la session de paiement
    if (SIMULATION_MODE) {
        // Mode simulation pour développement local
        $result = createSimulatedSession($data);
    } else {
        // Mode production - appel API réel
        $result = createCheckoutSession($data);
    }
    
    echo json_encode($result);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Méthode non autorisée'
    ]);
}

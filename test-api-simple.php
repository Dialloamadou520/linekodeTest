<?php
/**
 * Test Simple de l'API DexpayAfrica
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$apiKey = getenv('DEXPAY_API_KEY') ?: 'pk_live_VOTRE_CLE_PUBLIQUE';
$baseUrl = 'https://api.dexpay.africa/api/v1';

echo "=== TEST API DEXPAYAFRICA ===\n\n";

// Test 1: Vérifier cURL
echo "1. Test cURL: ";
if (function_exists('curl_init')) {
    echo "✅ OK\n";
} else {
    echo "❌ ERREUR - cURL non disponible\n";
    exit();
}

// Test 2: Connexion de base
echo "\n2. Test connexion à l'API...\n";
$ch = curl_init($baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ ERREUR: $error\n";
    exit();
}

echo "✅ Connexion OK (HTTP $httpCode)\n";

// Test 3: Création de session
echo "\n3. Test création de session...\n";

$payload = [
    'reference' => 'TEST_' . uniqid() . '_' . time(),
    'item_name' => 'Test API',
    'amount' => 1000,
    'currency' => 'XOF',
    'countryISO' => 'SN',
    'webhook_url' => 'https://webhook.site/unique-id',
    'success_url' => 'https://votre-site.com/success',
    'failure_url' => 'https://votre-site.com/failure',
    'custom_metadata' => [
        'test' => true,
        'source' => 'test_simple'
    ]
];

echo "Payload:\n";
echo json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init($baseUrl . '/checkout-sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-api-key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ ERREUR cURL: $error\n";
    exit();
}

echo "HTTP Code: $httpCode\n";
echo "Réponse:\n";
$apiResponse = json_decode($response, true);
echo json_encode($apiResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if ($httpCode === 200 || $httpCode === 201) {
    echo "✅ SESSION CRÉÉE AVEC SUCCÈS !\n";
    echo "Reference: " . ($apiResponse['data']['reference'] ?? 'N/A') . "\n";
    echo "Payment URL: " . ($apiResponse['data']['payment_url'] ?? 'N/A') . "\n";
} else {
    echo "❌ ERREUR API\n";
    echo "Message: " . ($apiResponse['message'] ?? 'Erreur inconnue') . "\n";
}

echo "\n=== FIN DU TEST ===\n";

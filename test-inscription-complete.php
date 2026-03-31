<?php
/**
 * Test d'inscription complète avec création de session de paiement
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TEST INSCRIPTION COMPLÈTE LINEKODE ===\n\n";

// Données de test
$testData = [
    'amount' => 50000,
    'customer_phone' => '+221771234567',
    'customer_email' => 'test@linekode.com',
    'description' => 'Inscription Formation Linekode - Test',
    'success_url' => 'http://localhost/linekode-PAIEMENT-DIRECT/payment-success.php',
    'cancel_url' => 'http://localhost/linekode-PAIEMENT-DIRECT/payment-cancelled.php',
    'webhook_url' => 'http://localhost/linekode-PAIEMENT-DIRECT/api/dexpay-webhook.php',
    'metadata' => [
        'inscription_id' => 'TEST_' . time(),
        'source' => 'test_script'
    ]
];

echo "1. Données de test:\n";
echo json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test de l'API dexpay-checkout.php
echo "2. Appel de l'API dexpay-checkout.php...\n";

$ch = curl_init('http://localhost/linekode-PAIEMENT-DIRECT/api/dexpay-checkout.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ ERREUR cURL: $error\n";
    exit(1);
}

echo "HTTP Code: $httpCode\n";
echo "Réponse:\n";
$result = json_decode($response, true);
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Vérifier le résultat
if ($httpCode === 200 && isset($result['success']) && $result['success']) {
    echo "✅ SESSION DE PAIEMENT CRÉÉE AVEC SUCCÈS !\n\n";
    
    echo "Détails de la session:\n";
    echo "  - Référence: " . ($result['reference'] ?? 'N/A') . "\n";
    echo "  - Session ID: " . ($result['session_id'] ?? 'N/A') . "\n";
    echo "  - URL de paiement: " . ($result['checkout_url'] ?? 'N/A') . "\n";
    echo "  - Montant: 50 000 XOF\n";
    echo "  - Email: " . $testData['customer_email'] . "\n";
    echo "  - Téléphone: " . $testData['customer_phone'] . "\n\n";
    
    if (isset($result['checkout_url'])) {
        echo "🔗 Lien de paiement généré:\n";
        echo $result['checkout_url'] . "\n\n";
        echo "👉 L'utilisateur serait redirigé vers cette URL pour effectuer le paiement.\n";
    }
    
    echo "\n✅ TEST RÉUSSI - L'intégration fonctionne correctement !\n";
} else {
    echo "❌ ERREUR lors de la création de la session\n";
    echo "Message: " . ($result['error'] ?? 'Erreur inconnue') . "\n";
    
    if (isset($result['details'])) {
        echo "Détails: " . json_encode($result['details'], JSON_PRETTY_PRINT) . "\n";
    }
}

echo "\n=== FIN DU TEST ===\n";

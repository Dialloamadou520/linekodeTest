<?php
/**
 * Test d'inscription avec URLs de production
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TEST INSCRIPTION AVEC URLS DE PRODUCTION ===\n\n";

// Données de test avec URLs de production
$testData = [
    'amount' => 50000,
    'customer_phone' => '+221771234567',
    'customer_email' => 'test@linekode.com',
    'description' => 'Inscription Formation Linekode - Test Production',
    'metadata' => [
        'inscription_id' => 'TEST_' . time(),
        'source' => 'test_production'
    ]
    // Les URLs success_url, cancel_url et webhook_url seront utilisées par défaut depuis dexpay-checkout.php
];

echo "1. Données de test:\n";
echo json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "Note: Les URLs de redirection utilisent les valeurs par défaut:\n";
echo "  - success_url: https://linekode.com/payment-success.php\n";
echo "  - failure_url: https://linekode.com/payment-cancelled.php\n";
echo "  - webhook_url: https://linekode.com/api/dexpay-webhook.php\n\n";

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
    
    echo "═══════════════════════════════════════════════\n";
    echo "         DÉTAILS DE LA SESSION DE PAIEMENT      \n";
    echo "═══════════════════════════════════════════════\n\n";
    
    echo "📋 Référence: " . ($result['reference'] ?? 'N/A') . "\n";
    echo "🆔 Session ID: " . ($result['session_id'] ?? 'N/A') . "\n";
    echo "💰 Montant: 50 000 XOF\n";
    echo "📧 Email: " . $testData['customer_email'] . "\n";
    echo "📱 Téléphone: " . $testData['customer_phone'] . "\n\n";
    
    if (isset($result['checkout_url'])) {
        echo "🔗 URL DE PAIEMENT:\n";
        echo "   " . $result['checkout_url'] . "\n\n";
        echo "═══════════════════════════════════════════════\n";
        echo "✅ FLUX DE PAIEMENT:\n";
        echo "═══════════════════════════════════════════════\n";
        echo "1. L'utilisateur remplit le formulaire d'inscription\n";
        echo "2. Le système crée une session de paiement DexpayAfrica\n";
        echo "3. L'utilisateur est redirigé vers: " . $result['checkout_url'] . "\n";
        echo "4. L'utilisateur choisit son mode de paiement (Wave, Orange Money, etc.)\n";
        echo "5. Après paiement:\n";
        echo "   - Succès → https://linekode.com/payment-success.php\n";
        echo "   - Échec → https://linekode.com/payment-cancelled.php\n";
        echo "6. DexpayAfrica envoie une notification webhook à:\n";
        echo "   → https://linekode.com/api/dexpay-webhook.php\n\n";
    }
    
    echo "═══════════════════════════════════════════════\n";
    echo "✅ TEST RÉUSSI - L'INTÉGRATION FONCTIONNE !\n";
    echo "═══════════════════════════════════════════════\n";
} else {
    echo "❌ ERREUR lors de la création de la session\n";
    echo "Message: " . ($result['error'] ?? 'Erreur inconnue') . "\n";
    
    if (isset($result['details'])) {
        echo "\nDétails de l'erreur:\n";
        echo json_encode($result['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}

echo "\n=== FIN DU TEST ===\n";

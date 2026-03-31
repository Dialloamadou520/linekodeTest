<?php
/**
 * Création de liens de paiement directs pour les opérateurs
 * Sans passer par l'API DexpayAfrica
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Lire les données
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validation
if (!isset($data['operator']) || !isset($data['amount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Opérateur et montant requis']);
    exit;
}

$operator = $data['operator'];
$amount = $data['amount'];
$description = $data['description'] ?? 'Inscription Formation Linekode';
$reference = 'LINEKODE_' . uniqid() . '_' . time();

// Configuration des opérateurs
$operators = [
    'wave' => [
        'name' => 'Wave',
        'merchant_id' => 'cos-23x70p69010nj', // Votre ID marchand Wave
        'url_template' => 'https://pay.wave.com/c/{merchant_id}?a={amount}&c=XOF&m={description}'
    ],
    'orange_money' => [
        'name' => 'Orange Money',
        'merchant_id' => 'VOTRE_ID_ORANGE', // À configurer
        'url_template' => 'https://payment.orange-money.com/pay?merchant={merchant_id}&amount={amount}&currency=XOF&description={description}'
    ],
    'mtn' => [
        'name' => 'MTN Mobile Money',
        'merchant_id' => 'VOTRE_ID_MTN', // À configurer
        'url_template' => 'https://mtn-momo.com/pay?merchant={merchant_id}&amount={amount}&currency=XOF&description={description}'
    ],
    'moov' => [
        'name' => 'Moov Money',
        'merchant_id' => 'VOTRE_ID_MOOV', // À configurer
        'url_template' => 'https://moov-money.com/pay?merchant={merchant_id}&amount={amount}&currency=XOF&description={description}'
    ]
];

// Vérifier si l'opérateur existe
if (!isset($operators[$operator])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Opérateur non supporté']);
    exit;
}

$config = $operators[$operator];

// Créer l'URL de paiement
$paymentUrl = str_replace(
    ['{merchant_id}', '{amount}', '{description}'],
    [$config['merchant_id'], $amount, urlencode($description)],
    $config['url_template']
);

// Log pour debug
error_log("🔗 Création lien de paiement:");
error_log("  Opérateur: " . $config['name']);
error_log("  Montant: " . $amount . " XOF");
error_log("  Référence: " . $reference);
error_log("  URL: " . $paymentUrl);

// Sauvegarder la transaction (optionnel)
// Vous pouvez sauvegarder dans une base de données ici

// Retourner la réponse
echo json_encode([
    'success' => true,
    'reference' => $reference,
    'operator' => $config['name'],
    'payment_url' => $paymentUrl,
    'amount' => $amount,
    'currency' => 'XOF'
]);

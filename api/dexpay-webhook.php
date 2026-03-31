<?php
/**
 * Webhook DexpayAfrica pour Linekode
 * Reçoit les notifications de paiement de DexpayAfrica
 */

header('Content-Type: application/json');

// Configuration
define('DEXPAY_SECRET_KEY', getenv('DEXPAY_SECRET_KEY') ?: 'sk_live_VOTRE_CLE_SECRETE');

// Lire les données du webhook
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log pour debug
error_log("📥 Webhook DexpayAfrica reçu: " . $input);

// Vérifier que les données sont valides
if (!$data) {
    error_log("❌ Webhook: Données invalides");
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

// Vérifier la signature (si DexpayAfrica fournit une signature)
// TODO: Implémenter la vérification de signature si disponible

// Traiter l'événement
$event = $data['event'] ?? '';
$paymentData = $data['data'] ?? [];

error_log("🔔 Event: " . $event);
error_log("📊 Data: " . json_encode($paymentData));

switch ($event) {
    case 'checkout.completed':
    case 'payment.success':
        // Paiement réussi
        $reference = $paymentData['reference'] ?? '';
        $amount = $paymentData['amount'] ?? 0;
        $status = $paymentData['status'] ?? '';
        
        error_log("✅ Paiement réussi - Référence: $reference, Montant: $amount");
        
        // Ici vous pouvez:
        // 1. Mettre à jour votre base de données
        // 2. Envoyer un email de confirmation
        // 3. Activer l'accès à la formation
        // 4. Notifier l'administrateur
        
        // Exemple: Sauvegarder dans un fichier
        $paymentsFile = __DIR__ . '/../data/payments.json';
        $payments = [];
        
        if (file_exists($paymentsFile)) {
            $payments = json_decode(file_get_contents($paymentsFile), true) ?? [];
        }
        
        $payments[] = [
            'reference' => $reference,
            'amount' => $amount,
            'status' => 'completed',
            'event' => $event,
            'data' => $paymentData,
            'received_at' => date('Y-m-d H:i:s')
        ];
        
        // Créer le dossier data s'il n'existe pas
        if (!is_dir(__DIR__ . '/../data')) {
            mkdir(__DIR__ . '/../data', 0755, true);
        }
        
        file_put_contents($paymentsFile, json_encode($payments, JSON_PRETTY_PRINT));
        
        break;
        
    case 'checkout.failed':
    case 'payment.failed':
        // Paiement échoué
        $reference = $paymentData['reference'] ?? '';
        error_log("❌ Paiement échoué - Référence: $reference");
        
        break;
        
    case 'checkout.cancelled':
    case 'payment.cancelled':
        // Paiement annulé
        $reference = $paymentData['reference'] ?? '';
        error_log("⚠️ Paiement annulé - Référence: $reference");
        
        break;
        
    default:
        error_log("⚠️ Event non géré: $event");
        break;
}

// Répondre à DexpayAfrica
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Webhook processed',
    'event' => $event
]);

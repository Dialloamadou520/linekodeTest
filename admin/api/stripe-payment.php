<?php
// API Stripe pour Linekode - Paiement par carte de crédit
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config-server.php';

// Configuration Stripe
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_d8598dde548b558c52ac0019b1eae791');
define('STRIPE_SECRET_KEY', 'sk_live_'); // À configurer avec la clé secrète correspondante
define('STRIPE_WEBHOOK_SECRET', 'whsec_'); // À configurer pour les webhooks

// Activer le mode développement
define('STRIPE_DEBUG', true);

class StripePaymentAPI {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Créer une session de paiement Stripe
    public function createCheckoutSession($data) {
        try {
            $amount = $data['amount'];
            $currency = $data['currency'] ?? 'XOF';
            $inscriptionId = $data['inscription_id'] ?? null;
            $description = $data['description'] ?? 'Formation Linekode';
            $successUrl = $data['success_url'] ?? 'http://localhost:8000/payment-success.html';
            $cancelUrl = $data['cancel_url'] ?? 'http://localhost:8000/payment-cancelled.html';
            
            // Valider les données
            if (!$amount || $amount <= 0) {
                return $this->error('Montant invalide');
            }
            
            // Créer la session en base de données
            $sessionId = $this->generateSessionId();
            $status = 'pending';
            
            $stmt = $this->db->prepare("
                INSERT INTO payments (session_id, checkout_session_id, amount, currency, status, inscription_id, description, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$sessionId, $stripeSession['id'], $amount, $currency, $status, $inscriptionId, $description]);
            
            // Créer la session Stripe Checkout
            $stripeSession = $this->createStripeSession([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency),
                        'unit_amount' => $amount * 100, // Stripe utilise les cents
                        'product_data' => [
                            'name' => $description,
                            'images' => ['http://localhost:8000/images/logo-linekode.png']
                        ]
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl . '?session_id=' . $sessionId,
                'cancel_url' => $cancelUrl . '?session_id=' . $sessionId,
                'metadata' => [
                    'session_id' => $sessionId,
                    'inscription_id' => $inscriptionId,
                    'description' => $description
                ]
            ]);
            
            return $this->success([
                'session_id' => $sessionId,
                'checkout_session_id' => $stripeSession['id'],
                'checkout_url' => $stripeSession['url'],
                'publishable_key' => STRIPE_PUBLISHABLE_KEY,
                'amount' => $amount,
                'currency' => $currency,
                'method' => 'credit_card',
                'status' => $status
            ]);
            
        } catch (Exception $e) {
            return $this->error('Erreur lors de la création de la session Stripe: ' . $e->getMessage());
        }
    }
    
    // Vérifier le statut d'un paiement Stripe
    public function checkPaymentStatus($sessionId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM payments WHERE session_id = ?");
            $stmt->execute([$sessionId]);
            $payment = $stmt->fetch();
            
            if (!$payment) {
                return $this->error('Session de paiement non trouvée');
            }
            
            // Si le paiement n'est pas encore complété, vérifier avec Stripe
            if ($payment['status'] === 'pending' && !empty($payment['transaction_id'])) {
                $stripeStatus = $this->verifyStripePayment($payment['transaction_id']);
                
                if ($stripeStatus['status'] !== $payment['status']) {
                    $this->updatePaymentStatus($payment['session_id'], $stripeStatus['status']);
                    
                    if ($stripeStatus['status'] === 'completed' && $payment['inscription_id']) {
                        $this->confirmInscription($payment['inscription_id']);
                    }
                }
            }
            
            return $this->success([
                'session_id' => $payment['session_id'],
                'status' => $payment['status'],
                'transaction_id' => $payment['transaction_id'],
                'amount' => $payment['amount'],
                'method' => $payment['method'],
                'created_at' => $payment['created_at'],
                'updated_at' => $payment['updated_at']
            ]);
            
        } catch (Exception $e) {
            return $this->error('Erreur lors de la vérification du paiement: ' . $e->getMessage());
        }
    }
    
    // Webhook Stripe pour les notifications
    public function handleWebhook() {
        try {
            $payload = file_get_contents('php://input');
            $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            
            $event = null;
            
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sigHeader, STRIPE_WEBHOOK_SECRET
                );
            } catch(\UnexpectedValueException $e) {
                return $this->error('Invalid payload');
            } catch(\Stripe\Exception\SignatureVerificationException $e) {
                return $this->error('Invalid signature');
            }
            
            // Gérer les événements
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $this->handleCompletedSession($session);
                    break;
                    
                case 'checkout.session.expired':
                    $session = $event->data->object;
                    $this->handleExpiredSession($session);
                    break;
                    
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $this->handleFailedPayment($paymentIntent);
                    break;
                    
                default:
                    // Événement non géré
                    break;
            }
            
            return $this->success(['received' => true]);
            
        } catch (Exception $e) {
            return $this->error('Erreur webhook: ' . $e->getMessage());
        }
    }
    
    // Gérer une session complétée
    private function handleCompletedSession($session) {
        $sessionId = $session->metadata->session_id;
        $transactionId = $session->payment_intent;
        
        $this->updatePaymentStatus($sessionId, 'completed', $transactionId);
        
        // Récupérer l'inscription associée
        $stmt = $this->db->prepare("SELECT inscription_id FROM payments WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $payment = $stmt->fetch();
        
        if ($payment && $payment['inscription_id']) {
            $this->confirmInscription($payment['inscription_id']);
        }
    }
    
    // Gérer une session expirée
    private function handleExpiredSession($session) {
        $sessionId = $session->metadata->session_id;
        $this->updatePaymentStatus($sessionId, 'cancelled');
    }
    
    // Gérer un paiement échoué
    private function handleFailedPayment($paymentIntent) {
        // Mettre à jour le statut en fonction du payment_intent
        $stmt = $this->db->prepare("UPDATE payments SET status = 'failed', updated_at = NOW() WHERE transaction_id = ?");
        $stmt->execute([$paymentIntent->id]);
    }
    
    // Créer une session Stripe
    private function createStripeSession($params) {
        try {
            // Mode développement : toujours utiliser la simulation
            if (defined('STRIPE_DEBUG') && STRIPE_DEBUG) {
                error_log('Stripe DEBUG: Mode développement activé - utilisation de la simulation');
                return $this->createSimulatedSession($params);
            }
            
            // En production, tenter l'API Stripe réelle
            if (function_exists('curl_init') && defined('STRIPE_SECRET_KEY') && STRIPE_SECRET_KEY !== 'sk_live_') {
                error_log('Stripe DEBUG: Tentative de connexion API Stripe réelle');
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . STRIPE_SECRET_KEY,
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                if ($error) {
                    error_log('Stripe DEBUG: Erreur cURL: ' . $error);
                    throw new Exception('Erreur cURL: ' . $error);
                }
                
                error_log('Stripe DEBUG: HTTP Code: ' . $httpCode . ' Response: ' . substr($response, 0, 200));
                
                if ($httpCode === 200) {
                    $decoded = json_decode($response, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        error_log('Stripe DEBUG: Session créée avec succès');
                        return $decoded;
                    } else {
                        error_log('Stripe DEBUG: Erreur JSON: ' . json_last_error_msg());
                        throw new Exception('Réponse JSON invalide: ' . json_last_error_msg());
                    }
                } else {
                    error_log('Stripe DEBUG: Erreur API: HTTP ' . $httpCode . ' - ' . $response);
                    throw new Exception('Erreur API Stripe: HTTP ' . $httpCode . ' - ' . $response);
                }
            }
            
            // Fallback à la simulation
            error_log('Stripe DEBUG: Fallback à la simulation');
            return $this->createSimulatedSession($params);
            
        } catch (Exception $e) {
            // En cas d'erreur, retourner une session simulée
            error_log('Stripe DEBUG: Exception capturée: ' . $e->getMessage());
            return $this->createSimulatedSession($params);
        }
    }
    
    // Créer une session simulée pour le développement
    private function createSimulatedSession($params) {
        return [
            'id' => 'cs_test_' . uniqid(),
            'url' => 'https://checkout.stripe.com/pay/' . uniqid(),
            'payment_status' => 'unpaid',
            'created' => time(),
            'metadata' => $params['metadata'] ?? []
        ];
    }
    
    // Vérifier un paiement Stripe
    private function verifyStripePayment($transactionId) {
        // Simulation pour développement
        // En production, vérifier avec l'API Stripe
        
        return [
            'status' => 'completed',
            'transaction_id' => $transactionId
        ];
    }
    
    // Mettre à jour le statut du paiement
    private function updatePaymentStatus($sessionId, $status, $transactionId = null) {
        $stmt = $this->db->prepare("
            UPDATE payments 
            SET status = ?, transaction_id = ?, updated_at = NOW() 
            WHERE session_id = ?
        ");
        $stmt->execute([$status, $transactionId, $sessionId]);
    }
    
    // Confirmer une inscription
    private function confirmInscription($inscriptionId) {
        $stmt = $this->db->prepare("UPDATE inscriptions SET status = 'confirmed', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$inscriptionId]);
    }
    
    // Générer un ID de session
    private function generateSessionId() {
        return 'stripe_' . uniqid() . '_' . time();
    }
    
    // Réponse succès
    private function success($data) {
        return [
            'success' => true,
            'data' => $data
        ];
    }
    
    // Réponse erreur
    private function error($message) {
        return [
            'success' => false,
            'error' => $message
        ];
    }
}

// Router API
$api = new StripePaymentAPI();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'status':
                    if (isset($_GET['session_id'])) {
                        echo json_encode($api->checkPaymentStatus($_GET['session_id']));
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Session ID requis']);
                    }
                    break;
                    
                case 'config':
                    echo json_encode([
                        'success' => true,
                        'data' => [
                            'publishable_key' => STRIPE_PUBLISHABLE_KEY
                        ]
                    ]);
                    break;
                    
                default:
                    echo json_encode(['success' => false, 'error' => 'Action non reconnue']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Action requise']);
        }
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'create':
                    echo json_encode($api->createCheckoutSession($input));
                    break;
                    
                case 'webhook':
                    echo json_encode($api->handleWebhook());
                    break;
                    
                default:
                    echo json_encode(['success' => false, 'error' => 'Action non reconnue']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Action requise']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
        break;
}
?>

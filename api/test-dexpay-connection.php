<?php
/**
 * Script de Test de Connexion DexpayAfrica
 * Utilisez ce fichier pour tester la connexion entre votre site et l'API DexpayAfrica
 */

header('Content-Type: application/json');

// Configuration DexpayAfrica
$apiKey = getenv('DEXPAY_API_KEY') ?: 'pk_live_VOTRE_CLE_PUBLIQUE';
define('DEXPAY_API_KEY', $apiKey);
define('DEXPAY_SECRET_KEY', getenv('DEXPAY_SECRET_KEY') ?: 'sk_live_VOTRE_CLE_SECRETE');
define('DEXPAY_BASE_URL', 'https://api.dexpay.africa/api/v1');

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [],
    'api_tests' => [],
    'recommendations' => []
];

// 1. Vérifier les informations du serveur
$results['server_info'] = [
    'php_version' => phpversion(),
    'curl_enabled' => function_exists('curl_init'),
    'json_enabled' => function_exists('json_encode'),
    'openssl_enabled' => extension_loaded('openssl'),
    'allow_url_fopen' => ini_get('allow_url_fopen')
];

// 2. Test de résolution DNS
$results['api_tests']['dns_resolution'] = [
    'status' => 'testing',
    'message' => 'Test de résolution DNS pour api.dexpayafrica.com'
];

$host = 'api.dexpayafrica.com';
$ip = gethostbyname($host);
if ($ip !== $host) {
    $results['api_tests']['dns_resolution'] = [
        'status' => 'success',
        'ip_address' => $ip,
        'message' => 'DNS résolu avec succès'
    ];
} else {
    $results['api_tests']['dns_resolution'] = [
        'status' => 'failed',
        'message' => 'Impossible de résoudre le DNS. Vérifiez votre connexion internet.',
        'error' => 'DNS resolution failed'
    ];
}

// 3. Test de connexion HTTPS
$results['api_tests']['https_connection'] = [
    'status' => 'testing',
    'message' => 'Test de connexion HTTPS à DexpayAfrica'
];

$ch = curl_init(DEXPAY_BASE_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    $results['api_tests']['https_connection'] = [
        'status' => 'failed',
        'message' => 'Erreur de connexion HTTPS',
        'error' => $error,
        'http_code' => $httpCode
    ];
    $results['recommendations'][] = 'Vérifiez votre connexion internet et les paramètres du pare-feu';
} else {
    $results['api_tests']['https_connection'] = [
        'status' => 'success',
        'message' => 'Connexion HTTPS établie avec succès',
        'http_code' => $httpCode
    ];
}

// 4. Test de création de session (si connexion OK)
if ($results['api_tests']['https_connection']['status'] === 'success') {
    $results['api_tests']['session_creation'] = [
        'status' => 'testing',
        'message' => 'Test de création de session de paiement'
    ];
    
    $testData = [
        'reference' => 'TEST_' . uniqid() . '_' . time(),
        'item_name' => 'Test de connexion API',
        'item_price' => 1000,
        'currency' => 'XOF',
        'custom_metadata' => [
            'test' => true,
            'source' => 'connection_test'
        ]
    ];
    
    $ch = curl_init(DEXPAY_BASE_URL . '/checkout-sessions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . DEXPAY_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        $results['api_tests']['session_creation'] = [
            'status' => 'failed',
            'message' => 'Erreur lors de la création de session',
            'error' => $error,
            'http_code' => $httpCode
        ];
        $results['recommendations'][] = 'Vérifiez que votre serveur peut accéder à api.dexpayafrica.com';
    } else {
        $apiResponse = json_decode($response, true);
        
        if ($httpCode === 200 || $httpCode === 201) {
            $results['api_tests']['session_creation'] = [
                'status' => 'success',
                'message' => 'Session créée avec succès',
                'http_code' => $httpCode,
                'session_id' => $apiResponse['id'] ?? 'N/A',
                'response' => $apiResponse
            ];
        } else {
            $results['api_tests']['session_creation'] = [
                'status' => 'failed',
                'message' => 'Erreur API DexpayAfrica',
                'http_code' => $httpCode,
                'response' => $apiResponse
            ];
            
            if ($httpCode === 401) {
                $results['recommendations'][] = 'Clé API invalide. Vérifiez votre clé secrète DexpayAfrica';
            } elseif ($httpCode === 403) {
                $results['recommendations'][] = 'Accès refusé. Vérifiez les permissions de votre compte DexpayAfrica';
            } else {
                $results['recommendations'][] = 'Erreur API. Consultez la documentation DexpayAfrica ou contactez leur support';
            }
        }
    }
}

// 5. Résumé global
$allSuccess = true;
foreach ($results['api_tests'] as $test) {
    if ($test['status'] !== 'success') {
        $allSuccess = false;
        break;
    }
}

$results['summary'] = [
    'status' => $allSuccess ? 'success' : 'failed',
    'message' => $allSuccess 
        ? '✅ Connexion DexpayAfrica fonctionnelle ! Votre site peut accepter des paiements.'
        : '⚠️ Problèmes de connexion détectés. Consultez les recommandations ci-dessous.'
];

// Recommandations générales
if (!$results['server_info']['curl_enabled']) {
    $results['recommendations'][] = 'Extension PHP cURL non activée. Activez-la dans php.ini';
}

if (!$results['server_info']['openssl_enabled']) {
    $results['recommendations'][] = 'Extension PHP OpenSSL non activée. Nécessaire pour HTTPS';
}

// Afficher les résultats
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

<?php
// Script de test pour identifier le problème de transmission des données
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Collecter toutes les informations possibles
$debug = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'headers' => [
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'not set',
        'http_accept' => $_SERVER['HTTP_ACCEPT'] ?? 'not set'
    ],
    'php_input' => null,
    'post_data' => $_POST,
    'get_data' => $_GET,
    'raw_input_length' => 0,
    'json_decode_result' => null,
    'json_error' => null
];

// Lire php://input
$input = file_get_contents('php://input');
$debug['raw_input_length'] = strlen($input);
$debug['php_input'] = $input;

// Essayer de décoder le JSON
if (!empty($input)) {
    $decoded = json_decode($input, true);
    $debug['json_decode_result'] = $decoded;
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        $debug['json_error'] = json_last_error_msg();
    }
}

// Retourner toutes les informations
echo json_encode([
    'success' => true,
    'message' => 'Test de réception des données',
    'debug' => $debug
], JSON_PRETTY_PRINT);

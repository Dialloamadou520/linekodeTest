<?php
/**
 * Configuration centralisée pour Linekode
 * Copiez ce fichier en config.php et remplissez avec vos vraies valeurs
 */

// Charger les variables d'environnement depuis .env si le fichier existe
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Configuration DexpayAfrica
define('DEXPAY_API_KEY', $_ENV['DEXPAY_API_KEY'] ?? 'pk_live_VOTRE_CLE_PUBLIQUE');
define('DEXPAY_SECRET_KEY', $_ENV['DEXPAY_SECRET_KEY'] ?? 'sk_live_VOTRE_CLE_SECRETE');
define('DEXPAY_BASE_URL', $_ENV['DEXPAY_BASE_URL'] ?? 'https://api.dexpay.africa/api/v1');
define('DEXPAY_SANDBOX_MODE', filter_var($_ENV['DEXPAY_SANDBOX_MODE'] ?? 'false', FILTER_VALIDATE_BOOLEAN));

// URLs du site
define('SITE_URL', $_ENV['SITE_URL'] ?? 'https://linekode.com');
define('SUCCESS_URL', $_ENV['SUCCESS_URL'] ?? SITE_URL . '/payment-success.php');
define('FAILURE_URL', $_ENV['FAILURE_URL'] ?? SITE_URL . '/payment-cancelled.php');
define('WEBHOOK_URL', $_ENV['WEBHOOK_URL'] ?? SITE_URL . '/api/dexpay-webhook.php');

// Configuration générale
define('SITE_NAME', $_ENV['SITE_NAME'] ?? 'Linekode');
define('SITE_EMAIL', $_ENV['SITE_EMAIL'] ?? 'contact@linekode.com');
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL'] ?? 'admin@linekode.com');

// Montants et devises
define('INSCRIPTION_AMOUNT', (int)($_ENV['INSCRIPTION_AMOUNT'] ?? 50000));
define('COUNTRY_ISO', $_ENV['COUNTRY_ISO'] ?? 'SN');
define('CURRENCY', $_ENV['CURRENCY'] ?? 'XOF');

// Configuration email (optionnel)
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? '');
define('SMTP_PORT', (int)($_ENV['SMTP_PORT'] ?? 587));
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? '');
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? '');
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? SITE_EMAIL);
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? SITE_NAME);

// Configuration base de données (optionnel)
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'linekode_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');

// Mode debug
define('DEBUG_MODE', filter_var($_ENV['DEBUG_MODE'] ?? 'false', FILTER_VALIDATE_BOOLEAN));

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

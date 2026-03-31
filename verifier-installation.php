<?php
/**
 * Script de Vérification Post-Déploiement
 * À exécuter après avoir uploadé le site sur le serveur
 * URL: https://votre-domaine.com/verifier-installation.php
 * 
 * ⚠️ IMPORTANT : Supprimez ce fichier après vérification !
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Installation Linekode</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
        }
        .test {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        .info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }
        .icon {
            font-size: 20px;
            margin-right: 10px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .delete-warning {
            background: #dc3545;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Vérification de l'Installation Linekode</h1>
        <p><strong>Date :</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
        <p><strong>Serveur :</strong> <?php echo $_SERVER['SERVER_NAME']; ?></p>

        <?php
        $errors = 0;
        $warnings = 0;
        $success = 0;

        // Test 1: PHP Version
        echo "<h2>1. Configuration PHP</h2>";
        $phpVersion = phpversion();
        if (version_compare($phpVersion, '7.4.0', '>=')) {
            echo "<div class='test success'><span class='icon'>✅</span> PHP Version: <code>$phpVersion</code> (OK)</div>";
            $success++;
        } else {
            echo "<div class='test error'><span class='icon'>❌</span> PHP Version: <code>$phpVersion</code> (Minimum requis: 7.4)</div>";
            $errors++;
        }

        // Test 2: cURL
        if (function_exists('curl_init')) {
            $curlVersion = curl_version();
            echo "<div class='test success'><span class='icon'>✅</span> Extension cURL: Activée (Version " . $curlVersion['version'] . ")</div>";
            $success++;
        } else {
            echo "<div class='test error'><span class='icon'>❌</span> Extension cURL: Non activée - Contactez votre hébergeur</div>";
            $errors++;
        }

        // Test 3: Fichier .env
        echo "<h2>2. Fichiers de Configuration</h2>";
        if (file_exists(__DIR__ . '/.env')) {
            echo "<div class='test success'><span class='icon'>✅</span> Fichier <code>.env</code> : Trouvé</div>";
            $success++;
            
            // Vérifier le contenu
            $envContent = file_get_contents(__DIR__ . '/.env');
            if (strpos($envContent, 'DEXPAY_API_KEY') !== false) {
                echo "<div class='test success'><span class='icon'>✅</span> Variable <code>DEXPAY_API_KEY</code> : Configurée</div>";
                $success++;
            } else {
                echo "<div class='test error'><span class='icon'>❌</span> Variable <code>DEXPAY_API_KEY</code> : Manquante dans .env</div>";
                $errors++;
            }
            
            if (strpos($envContent, 'DEXPAY_SECRET_KEY') !== false) {
                echo "<div class='test success'><span class='icon'>✅</span> Variable <code>DEXPAY_SECRET_KEY</code> : Configurée</div>";
                $success++;
            } else {
                echo "<div class='test error'><span class='icon'>❌</span> Variable <code>DEXPAY_SECRET_KEY</code> : Manquante dans .env</div>";
                $errors++;
            }
        } else {
            echo "<div class='test error'><span class='icon'>❌</span> Fichier <code>.env</code> : Non trouvé - Créez-le à partir de .env.example</div>";
            $errors++;
        }

        // Test 4: config.php
        if (file_exists(__DIR__ . '/config.php')) {
            echo "<div class='test success'><span class='icon'>✅</span> Fichier <code>config.php</code> : Trouvé</div>";
            $success++;
            
            // Charger config.php
            require_once __DIR__ . '/config.php';
            
            if (defined('DEXPAY_API_KEY')) {
                $apiKey = DEXPAY_API_KEY;
                if ($apiKey !== 'pk_live_VOTRE_CLE_PUBLIQUE') {
                    echo "<div class='test success'><span class='icon'>✅</span> <code>DEXPAY_API_KEY</code> : Configurée (commence par " . substr($apiKey, 0, 15) . "...)</div>";
                    $success++;
                } else {
                    echo "<div class='test warning'><span class='icon'>⚠️</span> <code>DEXPAY_API_KEY</code> : Utilise la valeur par défaut - Configurez .env</div>";
                    $warnings++;
                }
            }
            
            if (defined('DEXPAY_SECRET_KEY')) {
                $secretKey = DEXPAY_SECRET_KEY;
                if ($secretKey !== 'sk_live_VOTRE_CLE_SECRETE') {
                    echo "<div class='test success'><span class='icon'>✅</span> <code>DEXPAY_SECRET_KEY</code> : Configurée</div>";
                    $success++;
                } else {
                    echo "<div class='test warning'><span class='icon'>⚠️</span> <code>DEXPAY_SECRET_KEY</code> : Utilise la valeur par défaut - Configurez .env</div>";
                    $warnings++;
                }
            }
            
            if (defined('SITE_URL')) {
                echo "<div class='test info'><span class='icon'>ℹ️</span> <code>SITE_URL</code> : " . SITE_URL . "</div>";
            }
            
            if (defined('WEBHOOK_URL')) {
                echo "<div class='test info'><span class='icon'>ℹ️</span> <code>WEBHOOK_URL</code> : " . WEBHOOK_URL . "</div>";
            }
        } else {
            echo "<div class='test error'><span class='icon'>❌</span> Fichier <code>config.php</code> : Non trouvé - Créez-le à partir de config.example.php</div>";
            $errors++;
        }

        // Test 5: Dossier data
        echo "<h2>3. Dossiers et Permissions</h2>";
        if (is_dir(__DIR__ . '/data')) {
            echo "<div class='test success'><span class='icon'>✅</span> Dossier <code>data/</code> : Existe</div>";
            $success++;
            
            if (is_writable(__DIR__ . '/data')) {
                echo "<div class='test success'><span class='icon'>✅</span> Dossier <code>data/</code> : Accessible en écriture</div>";
                $success++;
            } else {
                echo "<div class='test error'><span class='icon'>❌</span> Dossier <code>data/</code> : Non accessible en écriture - Exécutez: <code>chmod 755 data</code></div>";
                $errors++;
            }
        } else {
            echo "<div class='test error'><span class='icon'>❌</span> Dossier <code>data/</code> : N'existe pas - Créez-le: <code>mkdir data && chmod 755 data</code></div>";
            $errors++;
        }

        // Test 6: Fichiers API
        echo "<h2>4. Fichiers API</h2>";
        $apiFiles = [
            'api/dexpay-checkout.php' => 'Endpoint de création de session',
            'api/dexpay-webhook.php' => 'Réception des webhooks',
            'inscription.php' => 'Formulaire d\'inscription',
            'payment-success.php' => 'Page de succès',
            'payment-cancelled.php' => 'Page d\'annulation'
        ];

        foreach ($apiFiles as $file => $description) {
            if (file_exists(__DIR__ . '/' . $file)) {
                echo "<div class='test success'><span class='icon'>✅</span> <code>$file</code> : Trouvé ($description)</div>";
                $success++;
            } else {
                echo "<div class='test error'><span class='icon'>❌</span> <code>$file</code> : Manquant</div>";
                $errors++;
            }
        }

        // Test 7: HTTPS
        echo "<h2>5. Sécurité</h2>";
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            echo "<div class='test success'><span class='icon'>✅</span> HTTPS : Activé (Sécurisé)</div>";
            $success++;
        } else {
            echo "<div class='test warning'><span class='icon'>⚠️</span> HTTPS : Non détecté - Assurez-vous d'avoir un certificat SSL</div>";
            $warnings++;
        }

        // Test 8: Test de connexion à l'API DexpayAfrica
        echo "<h2>6. Connexion API DexpayAfrica</h2>";
        if (function_exists('curl_init') && defined('DEXPAY_API_KEY') && DEXPAY_API_KEY !== 'pk_live_VOTRE_CLE_PUBLIQUE') {
            $ch = curl_init('https://api.dexpay.africa/api/v1/checkout-sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'x-api-key: ' . DEXPAY_API_KEY
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 || $httpCode === 401 || $httpCode === 405) {
                echo "<div class='test success'><span class='icon'>✅</span> API DexpayAfrica : Accessible (HTTP $httpCode)</div>";
                $success++;
            } else {
                echo "<div class='test warning'><span class='icon'>⚠️</span> API DexpayAfrica : Réponse inattendue (HTTP $httpCode)</div>";
                $warnings++;
            }
        } else {
            echo "<div class='test info'><span class='icon'>ℹ️</span> Test API : Ignoré (configurez d'abord les clés API)</div>";
        }

        // Résumé
        echo "<h2>📊 Résumé</h2>";
        echo "<div class='test info'>";
        echo "<p><strong>✅ Succès :</strong> $success</p>";
        echo "<p><strong>⚠️ Avertissements :</strong> $warnings</p>";
        echo "<p><strong>❌ Erreurs :</strong> $errors</p>";
        echo "</div>";

        if ($errors === 0 && $warnings === 0) {
            echo "<div class='test success' style='font-size: 18px; text-align: center;'>";
            echo "<span class='icon'>🎉</span> <strong>Installation Parfaite !</strong> Votre site est prêt pour la production.";
            echo "</div>";
        } elseif ($errors === 0) {
            echo "<div class='test warning' style='font-size: 18px; text-align: center;'>";
            echo "<span class='icon'>✅</span> <strong>Installation OK</strong> avec quelques avertissements mineurs.";
            echo "</div>";
        } else {
            echo "<div class='test error' style='font-size: 18px; text-align: center;'>";
            echo "<span class='icon'>❌</span> <strong>Des erreurs doivent être corrigées</strong> avant la mise en production.";
            echo "</div>";
        }

        // Actions recommandées
        echo "<h2>🔧 Actions Recommandées</h2>";
        echo "<div class='test info'>";
        echo "<ol>";
        if ($errors > 0) {
            echo "<li>Corrigez les erreurs ci-dessus</li>";
        }
        echo "<li>Configurez le webhook sur <a href='https://portal.dexpay.africa/webhooks' target='_blank'>https://portal.dexpay.africa/webhooks</a></li>";
        echo "<li>URL du webhook : <code>" . (defined('WEBHOOK_URL') ? WEBHOOK_URL : 'https://votre-domaine.com/api/dexpay-webhook.php') . "</code></li>";
        echo "<li>Testez un paiement sur <a href='inscription.php'>inscription.php</a></li>";
        echo "<li><strong>SUPPRIMEZ ce fichier après vérification !</strong></li>";
        echo "</ol>";
        echo "</div>";
        ?>

        <div class="delete-warning">
            ⚠️ IMPORTANT : Supprimez ce fichier après vérification !<br>
            Commande : <code>rm verifier-installation.php</code>
        </div>
    </div>
</body>
</html>

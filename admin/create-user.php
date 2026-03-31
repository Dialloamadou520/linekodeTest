<?php
// Script pour créer l'utilisateur MySQL - Linekode Admin
echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Créer Utilisateur MySQL - Linekode Admin</title>";
echo "    <style>";
echo "        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }";
echo "        .header { background: linear-gradient(135deg, #0284c7, #0369a1); color: white; padding: 30px; border-radius: 12px; text-align: center; margin-bottom: 30px; }";
echo "        .method { border: 1px solid #ddd; padding: 25px; margin: 20px 0; border-radius: 12px; background: #f8f9fa; }";
echo "        .method h3 { color: #0284c7; margin-top: 0; }";
echo "        .code-block { background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 8px; font-family: 'Courier New', monospace; margin: 15px 0; overflow-x: auto; }";
echo "        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #28a745; }";
echo "        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #0284c7; }";
echo "        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ffc107; }";
echo "        .btn { background: #0284c7; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }";
echo "        .btn:hover { background: #0369a1; }";
echo "        .btn-success { background: #28a745; }";
echo "        .btn-success:hover { background: #218838; }";
echo "        .btn-warning { background: #ffc107; color: #212529; }";
echo "        .btn-warning:hover { background: #e0a800; }";
echo "    </style>";
echo "</head>";
echo "<body>";

echo "<div class='header'>";
echo "    <h1>👤 Créer l'Utilisateur MySQL</h1>";
echo "    <h2>Linekode Admin - Configuration Utilisateur</h2>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>📋 Commande à exécuter:</h3>";
echo "<div class='code-block'>";
echo "CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';";
echo "</div>";
echo "</div>";

// Essayer de créer l'utilisateur automatiquement
$autoCreateSuccess = false;

echo "<div class='method'>";
echo "<h3>🚀 Méthode 1: Création Automatique</h3>";
echo "<p>Cliquez sur le bouton ci-dessous pour essayer de créer l'utilisateur automatiquement:</p>";

if (isset($_POST['auto_create'])) {
    try {
        // Essayer avec root sans mot de passe
        $pdo = new PDO('mysql:host=localhost', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Créer l'utilisateur
        $pdo->exec("CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024'");
        
        echo "<div class='success'>";
        echo "<h3>✅ Utilisateur créé avec succès!</h3>";
        echo "<p>L'utilisateur <strong>linekode_admin</strong> a été créé avec le mot de passe <strong>linekode2024</strong></p>";
        echo "</div>";
        
        $autoCreateSuccess = true;
        
        // Maintenant essayer de donner les permissions
        try {
            $pdo->exec("GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost'");
            $pdo->exec("FLUSH PRIVILEGES");
            echo "<div class='success'>";
            echo "<h3>✅ Permissions accordées!</h3>";
            echo "<p>L'utilisateur a maintenant tous les droits sur la base de données linekode_admin</p>";
            echo "</div>";
        } catch (Exception $e) {
            echo "<div class='warning'>";
            echo "<h3>⚠️ Permissions non accordées</h3>";
            echo "<p>Vous devrez donner les permissions manuellement. Voir Méthode 2.</p>";
            echo "</div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='warning'>";
        echo "<h3>⚠️ Création automatique échouée</h3>";
        echo "<p>Erreur: " . $e->getMessage() . "</p>";
        echo "<p>Veuillez utiliser une des méthodes manuelles ci-dessous.</p>";
        echo "</div>";
    }
}

if (!$autoCreateSuccess) {
    echo "<form method='post' style='margin-top: 20px;'>";
    echo "<button type='submit' name='auto_create' class='btn btn-success'>🚀 Créer l'utilisateur automatiquement</button>";
    echo "</form>";
}
echo "</div>";

echo "<div class='method'>";
echo "<h3>🖥️ Méthode 2: phpMyAdmin</h3>";
echo "<ol>";
echo "<li>Connectez-vous à phpMyAdmin</li>";
echo "<li>Cliquez sur l'onglet <strong>SQL</strong></li>";
echo "<li>Collez la commande suivante:</li>";
echo "</ol>";
echo "<div class='code-block'>";
echo "CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';";
echo "</div>";
echo "<ol start='4'>";
echo "<li>Cliquez sur <strong>Exécuter</strong></li>";
echo "<li>Puis donnez les permissions avec:</li>";
echo "</ol>";
echo "<div class='code-block'>";
echo "GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost';";
echo "FLUSH PRIVILEGES;";
echo "</div>";
echo "</div>";

echo "<div class='method'>";
echo "<h3>🔧 Méthode 3: cPanel</h3>";
echo "<ol>";
echo "<li>Connectez-vous à votre cPanel</li>";
echo "<li>Allez dans <strong>MySQL Databases</strong></li>";
echo "<li>Dans la section <strong>MySQL Users</strong></li>";
echo "<li>Username: <code>linekode_admin</code></li>";
echo "<li>Password: <code>linekode2024</code></li>";
echo "<li>Cliquez sur <strong>Create User</strong></li>";
echo "</ol>";
echo "</div>";

echo "<div class='method'>";
echo "<h3>💻 Méthode 4: Ligne de Commande (SSH)</h3>";
echo "<ol>";
echo "<li>Connectez-vous à votre serveur via SSH:</li>";
echo "</ol>";
echo "<div class='code-block'>";
echo "ssh utilisateur@votre-serveur.com";
echo "</div>";
echo "<ol start='2'>";
echo "<li>Connectez-vous à MySQL:</li>";
echo "</ol>";
echo "<div class='code-block'>";
echo "mysql -u root -p";
echo "</div>";
echo "<ol start='3'>";
echo "<li>Exécutez la commande:</li>";
echo "</ol>";
echo "<div class='code-block'>";
echo "CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';";
echo "GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost';";
echo "FLUSH PRIVILEGES;";
echo "EXIT;";
echo "</div>";
echo "</div>";

echo "<div class='method'>";
echo "<h3>🔧 Méthode 5: Client MySQL (Terminal)</h3>";
echo "<p>Si vous avez accès au terminal MySQL directement:</p>";
echo "<div class='code-block'>";
echo "mysql -u root -p";
echo "CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';";
echo "GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost';";
echo "FLUSH PRIVILEGES;";
echo "EXIT;";
echo "</div>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>📋 Récapitulatif de la configuration:</h3>";
echo "<ul>";
echo "<li><strong>Utilisateur:</strong> linekode_admin</li>";
echo "<li><strong>Mot de passe:</strong> linekode2024</li>";
echo "<li><strong>Hôte:</strong> localhost</li>";
echo "<li><strong>Permissions:</strong> Tous les privilèges sur linekode_admin</li>";
echo "</ul>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>⚠️ Points importants:</h3>";
echo "<ul>";
echo "<li>Assurez-vous que la base de données <code>linekode_admin</code> existe déjà</li>";
echo "<li>Si vous utilisez un mot de passe différent, notez-le bien</li>";
echo "<li>Après création, donnez les permissions sur la base de données</li>";
echo "<li>Utilisez <code>FLUSH PRIVILEGES</code> pour appliquer les changements</li>";
echo "</ul>";
echo "</div>";

echo "<div class='success'>";
echo "<h3>🎯 Prochaines étapes:</h3>";
echo "<ol>";
echo "<li>Vérifiez que l'utilisateur est créé: <code>SELECT User FROM mysql.user;</code></li>";
echo "<li>Vérifiez les permissions: <code>SHOW GRANTS FOR 'linekode_admin'@'localhost';</code></li>";
echo "<li>Accédez à l'installation: <a href='setup-server.php' class='btn'>setup-server.php</a></li>";
echo "<li>Connectez-vous à l'admin: <a href='login.php' class='btn'>login.php</a></li>";
echo "</ol>";
echo "</div>";

echo "<div style='margin-top: 30px; text-align: center;'>";
echo "<a href='setup-server.php' class='btn btn-success'>🚀 Continuer l'installation</a>";
echo "<a href='login.php' class='btn'>🔐 Accès admin</a>";
echo "<a href='../index.html' class='btn'>🏠 Retour au site</a>";
echo "</div>";

echo "</body>";
echo "</html>";
?>

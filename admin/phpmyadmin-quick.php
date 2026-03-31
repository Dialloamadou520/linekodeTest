<?php
// Guide rapide phpMyAdmin serveur - Linekode Admin
echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>phpMyAdmin Serveur - Linekode Admin</title>";
echo "    <style>";
echo "        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; }";
echo "        .header { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; border-radius: 12px; text-align: center; margin-bottom: 30px; }";
echo "        .command-box { background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 8px; font-family: 'Courier New', monospace; margin: 15px 0; border: 2px solid #28a745; }";
echo "        .step { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 12px; background: #f8f9fa; }";
echo "        .step h3 { color: #28a745; margin-top: 0; }";
echo "        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #28a745; }";
echo "        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #0284c7; }";
echo "        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ffc107; }";
echo "        .btn { background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; font-size: 16px; }";
echo "        .btn:hover { background: #218838; }";
echo "        .btn-primary { background: #0284c7; }";
echo "        .btn-primary:hover { background: #0369a1; }";
echo "        .copy-btn { background: #ffc107; color: #212529; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; font-size: 14px; }";
echo "        .copy-btn:hover { background: #e0a800; }";
echo "    </style>";
echo "</head>";
echo "<body>";

echo "<div class='header'>";
echo "    <h1>🌐 phpMyAdmin Serveur</h1>";
echo "    <h2>Instructions rapides pour Linekode Admin</h2>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>📋 Commande à exécuter dans phpMyAdmin:</h3>";
echo "<div class='command-box'>";
echo "CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';";
echo "</div>";
echo "<button class='copy-btn' onclick='copyCommand()'>📋 Copier la commande</button>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>🚀 Instructions phpMyAdmin:</h3>";
echo "<ol>";
echo "<li><strong>Connectez-vous</strong> à phpMyAdmin sur votre serveur</li>";
echo "<li><strong>Cliquez</strong> sur l'onglet <strong>SQL</strong> en haut</li>";
echo "<li><strong>Collez</strong> la commande ci-dessus dans la zone de texte</li>";
echo "<li><strong>Cliquez</strong> sur <strong>Exécuter</strong> (ou Go)</li>";
echo "<li><strong>Vérifiez</strong> le message de succès: <code># Query OK, 0 rows affected</code></li>";
echo "</ol>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>🔐 Donner les permissions (très important!):</h3>";
echo "<div class='command-box'>";
echo "GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost';";
echo "</div>";
echo "<button class='copy-btn' onclick='copyGrant()'>📋 Copier GRANT</button>";
echo "<ol>";
echo "<li><strong>Restez</strong> dans l'onglet SQL</li>";
echo "<li><strong>Collez</strong> cette commande</li>";
echo "<li><strong>Cliquez</strong> sur <strong>Exécuter</strong></li>";
echo "</ol>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>🔄 Appliquer les changements:</h3>";
echo "<div class='command-box'>";
echo "FLUSH PRIVILEGES;";
echo "</div>";
echo "<button class='copy-btn' onclick='copyFlush()'>📋 Copier FLUSH</button>";
echo "<ol>";
echo "<li><strong>Dernière</strong> commande dans l'onglet SQL</li>";
echo "<li><strong>Cliquez</strong> sur <strong>Exécuter</strong></li>";
echo "</ol>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>✅ Vérifier (optionnel):</h3>";
echo "<div class='command-box'>";
echo "SELECT User, Host FROM mysql.user WHERE User = 'linekode_admin';";
echo "</div>";
echo "<button class='copy-btn' onclick='copyVerify()'>📋 Copier Vérification</button>";
echo "<p><strong>Résultat attendu:</strong> <code>linekode_admin | localhost</code></p>";
echo "</div>";

echo "<div class='success'>";
echo "<h3>🎉 Une fois terminé:</h3>";
echo "<ul>";
echo "<li>Utilisateur créé: <strong>linekode_admin</strong></li>";
echo "<li>Mot de passe: <strong>linekode2024</strong></li>";
echo "<li>Permissions: <strong>Tous les privilèges</strong></li>";
echo "</ul>";
echo "<p>Accédez à l'installation: <a href='setup-server.php' class='btn btn-primary'>setup-server.php</a></p>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>⚠️ Si vous avez des erreurs:</h3>";
echo "<ul>";
echo "<li><strong>Access denied:</strong> Connectez-vous avec un utilisateur admin (root)</li>";
echo "<li><strong>Syntax error:</strong> Vérifiez les guillemets et apostrophes</li>";
echo "<li><strong>User already exists:</strong> C'est normal avec IF NOT EXISTS</li>";
echo "</ul>";
echo "</div>";

echo "<div class='info'>";
echo "<h3>📋 Toutes les commandes (copiez-les une par une):</h3>";
echo "<div class='command-box'>";
echo "-- 1. Créer l'utilisateur";
echo "CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';";
echo "";
echo "-- 2. Donner les permissions";
echo "GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost';";
echo "";
echo "-- 3. Appliquer les changements";
echo "FLUSH PRIVILEGES;";
echo "";
echo "-- 4. Vérifier (optionnel)";
echo "SELECT User, Host FROM mysql.user WHERE User = 'linekode_admin';";
echo "</div>";
echo "</div>";

echo "<div style='margin-top: 40px; text-align: center;'>";
echo "<a href='setup-server.php' class='btn btn-primary' style='font-size: 18px; padding: 15px 30px;'>🚀 Lancer l'installation</a>";
echo "<br><br>";
echo "<a href='login.php' class='btn'>🔐 Accès admin</a>";
echo "<a href='../index.html' class='btn'>🏠 Retour au site</a>";
echo "</div>";

echo "<script>";
echo "function copyCommand() {";
echo "    navigator.clipboard.writeText('CREATE USER IF NOT EXISTS \\'linekode_admin\\'@\\'localhost\\' IDENTIFIED BY \\'linekode2024\\';');";
echo "    alert('Commande copiée!');";
echo "}";
echo "function copyGrant() {";
echo "    navigator.clipboard.writeText('GRANT ALL PRIVILEGES ON linekode_admin.* TO \\'linekode_admin\\'@\\'localhost\\';');";
echo "    alert('Commande GRANT copiée!');";
echo "}";
echo "function copyFlush() {";
echo "    navigator.clipboard.writeText('FLUSH PRIVILEGES;');";
echo "    alert('Commande FLUSH copiée!');";
echo "}";
echo "function copyVerify() {";
echo "    navigator.clipboard.writeText('SELECT User, Host FROM mysql.user WHERE User = \\'linekode_admin\\';');";
echo "    alert('Commande de vérification copiée!');";
echo "}";
echo "</script>";

echo "</body>";
echo "</html>";
?>

<?php
require_once 'config-server.php';

echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Installation Linekode Admin</title>";
echo "    <style>";
echo "        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }";
echo "        .card { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }";
echo "        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .btn { background: #0284c7; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }";
echo "        .btn:hover { background: #0369a1; }";
echo "    </style>";
echo "</head>";
echo "<body>";

echo "<h1>🚀 Installation de Linekode Admin</h1>";

try {
    // Étape 1: Créer la base de données
    echo "<div class='card'>";
    echo "<h2>📦 Étape 1: Création de la base de données</h2>";
    
    $db = $database->getConnection();
    $database->createTables();
    
    echo "<div class='success'>✅ Base de données créée avec succès</div>";
    echo "<ul>";
    echo "<li>✅ Table 'inscriptions' créée</li>";
    echo "<li>✅ Table 'annonces' créée</li>";
    echo "<li>✅ Table 'messages' créée</li>";
    echo "<li>✅ Table 'admin_users' créée</li>";
    echo "<li>✅ Table 'settings' créée</li>";
    echo "</ul>";
    echo "</div>";

    // Étape 2: Vérifier l'utilisateur admin
    echo "<div class='card'>";
    echo "<h2>👤 Étape 2: Vérification de l'utilisateur admin</h2>";
    
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<div class='success'>✅ Utilisateur admin trouvé</div>";
        echo "<ul>";
        echo "<li>👤 Nom d'utilisateur: admin</li>";
        echo "<li>🔐 Mot de passe: linekode2024</li>";
        echo "<li>📧 Email: {$admin['email']}</li>";
        echo "</ul>";
    } else {
        echo "<div class='error'>❌ Utilisateur admin non trouvé</div>";
        echo "<p>Veuillez vérifier la configuration.</p>";
    }
    echo "</div>";

    // Étape 3: Configuration PHP
    echo "<div class='card'>";
    echo "<h2>⚙️ Étape 3: Configuration PHP</h2>";
    
    $phpVersion = phpversion();
    $pdoEnabled = extension_loaded('pdo');
    $pdoMysqlEnabled = extension_loaded('pdo_mysql');
    
    echo "<ul>";
    echo "<li>🐘 Version PHP: " . $phpVersion . " (>= 7.4 recommandé)</li>";
    echo "<li>🗄️ PDO: " . ($pdoEnabled ? "✅ Activé" : "❌ Non activé") . "</li>";
    echo "<li>🗄️ PDO MySQL: " . ($pdoMysqlEnabled ? "✅ Activé" : "❌ Non activé") . "</li>";
    echo "</ul>";
    
    if (!$pdoEnabled || !$pdoMysqlEnabled) {
        echo "<div class='error'>❌ Extensions PDO manquantes</div>";
        echo "<p>Veuillez activer les extensions PDO et PDO_Mysql dans votre configuration PHP.</p>";
    }
    echo "</div>";

    // Étape 4: Test de connexion
    echo "<div class='card'>";
    echo "<h2>🔗 Étape 4: Test de connexion</h2>";
    
    try {
        $stmt = $db->query("SELECT COUNT(*) as total FROM inscriptions");
        $result = $stmt->fetch();
        echo "<div class='success'>✅ Connexion à la base de données réussie</div>";
        echo "<p>Nombre d'inscriptions: {$result['total']}</p>";
    } catch(PDOException $e) {
        echo "<div class='error'>❌ Erreur de connexion: " . $e->getMessage() . "</div>";
    }
    echo "</div>";

    // Étape 5: Instructions finales
    echo "<div class='card'>";
    echo "<h2>🎉 Installation terminée !</h2>";
    echo "<div class='success'>✅ Linekode Admin est prêt à être utilisé</div>";
    echo "<h3>Prochaines étapes:</h3>";
    echo "<ol>";
    echo "<li>Connectez-vous à l'administration: <a href='login.php'>login.php</a></li>";
    echo "<li>Nom d'utilisateur: <strong>admin</strong></li>";
    echo "<li>Mot de passe: <strong>linekode2024</strong></li>";
    echo "<li>Commencez à gérer vos inscriptions et messages</li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Erreur lors de l'installation</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<div class='card'>";
echo "<h3>🔗 Liens utiles</h3>";
echo "<a href='login.php' class='btn'>🚀 Accéder à l'admin</a>";
echo "<a href='dashboard.php' class='btn'>📊 Dashboard</a>";
echo "<a href='inscriptions.php' class='btn'>👥 Inscriptions</a>";
echo "<a href='messages.php' class='btn'>📧 Messages</a>";
echo "<a href='annonces.php' class='btn'>📢 Annonces</a>";
echo "</div>";

echo "</body>";
echo "</html>";
?>


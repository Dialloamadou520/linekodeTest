<?php
// Script d'aide pour configurer MySQL - Linekode Admin
echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Configuration MySQL - Linekode Admin</title>";
echo "    <style>";
echo "        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }";
echo "        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .btn { background: #0284c7; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }";
echo "        .btn:hover { background: #0369a1; }";
echo "        code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }";
echo "        pre { background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto; }";
echo "    </style>";
echo "</head>";
echo "<body>";

echo "<h1>🔧 Configuration MySQL pour Linekode Admin</h1>";

// Vérifier si MySQL est disponible
if (!extension_loaded('pdo_mysql')) {
    echo "<div class='error'>";
    echo "<h3>❌ Extension PDO MySQL non activée</h3>";
    echo "<p>Veuillez activer l'extension PDO MySQL dans votre fichier php.ini:</p>";
    echo "<pre>extension=pdo_mysql</pre>";
    echo "</div>";
    echo "</body></html>";
    exit;
}

// Essayer de se connecter à MySQL
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>";
    echo "<h3>✅ Connexion MySQL réussie!</h3>";
    echo "<p>Nous pouvons maintenant créer la base de données et les tables.</p>";
    echo "</div>";
    
    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS linekode_admin");
    echo "<div class='success'>";
    echo "<h3>✅ Base de données 'linekode_admin' créée</h3>";
    echo "</div>";
    
    // Créer l'utilisateur
    $pdo->exec("CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024'");
    $pdo->exec("GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost'");
    $pdo->exec("FLUSH PRIVILEGES");
    echo "<div class='success'>";
    echo "<h3>✅ Utilisateur 'linekode_admin' créé avec les permissions</h3>";
    echo "</div>";
    
    // Se connecter à la nouvelle base de données
    $pdo = new PDO('mysql:host=localhost;dbname=linekode_admin', 'linekode_admin', 'linekode2024');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer les tables
    $tables = [
        "CREATE TABLE IF NOT EXISTS inscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            formation VARCHAR(255),
            niveau VARCHAR(50),
            motivation TEXT,
            address TEXT,
            status ENUM('new', 'pending', 'confirmed', 'cancelled') DEFAULT 'new',
            date DATE,
            source VARCHAR(50) DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS annonces (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            status ENUM('draft', 'published', 'scheduled') DEFAULT 'draft',
            date DATE,
            author VARCHAR(100) DEFAULT 'Admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(255),
            content TEXT,
            date DATETIME,
            read_status BOOLEAN DEFAULT 0,
            source VARCHAR(50) DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            description TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    echo "<div class='success'>";
    echo "<h3>✅ Tables créées avec succès</h3>";
    echo "</div>";
    
    // Insérer les données initiales
    $pdo->exec("INSERT IGNORE INTO admin_users (username, password, email) VALUES ('admin', '" . password_hash('linekode2024', PASSWORD_DEFAULT) . "', 'admin@linekode.sn')");
    
    $settings = [
        'site_name' => 'Linekode',
        'admin_email' => 'admin@linekode.sn',
        'currency' => 'FCFA',
        'company_name' => 'Linekode Sénégal',
        'company_phone' => '+221 77 123 45 67',
        'company_address' => 'Dakar, Sénégal'
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?)");
    foreach ($settings as $key => $value) {
        $stmt->execute([$key, $value, "Paramètre: $key"]);
    }
    
    $pdo->exec("INSERT IGNORE INTO annonces (title, content, status, date, author) VALUES ('Bienvenue sur Linekode', 'Nous sommes ravis de vous accueillir dans notre école de formation en développement web.', 'published', CURDATE(), 'Admin')");
    
    echo "<div class='success'>";
    echo "<h3>✅ Données initiales insérées</h3>";
    echo "</div>";
    
    echo "<div class='success'>";
    echo "<h2>🎉 Installation terminée avec succès!</h2>";
    echo "<p><strong>Base de données:</strong> linekode_admin</p>";
    echo "<p><strong>Utilisateur:</strong> linekode_admin</p>";
    echo "<p><strong>Mot de passe:</strong> linekode2024</p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Erreur de connexion MySQL</h3>";
    echo "<p><strong>Erreur:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>🛠️ Solutions possibles:</h3>";
    
    echo "<h4>Option 1: XAMPP</h4>";
    echo "<ol>";
    echo "<li>Démarrez XAMPP Control Panel</li>";
    echo "<li>Démarrez le service MySQL</li>";
    echo "<li>Accédez à phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
    echo "<li>Cliquez sur l'onglet 'SQL'</li>";
    echo "<li>Exécutez les commandes ci-dessous</li>";
    echo "</ol>";
    
    echo "<h4>Option 2: Commandes MySQL</h4>";
    echo "<pre>";
    echo "CREATE DATABASE linekode_admin;";
    echo "CREATE USER 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';";
    echo "GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost';";
    echo "FLUSH PRIVILEGES;";
    echo "</pre>";
    
    echo "<h4>Option 3: Utiliser un autre utilisateur</h4>";
    echo "<p>Si vous avez déjà un utilisateur MySQL, modifiez le fichier config-fixed.php:</p>";
    echo "<pre>";
    echo "private \$username = 'votre_utilisateur';";
    echo "private \$password = 'votre_mot_de_passe';";
    echo "</pre>";
    echo "</div>";
}

echo "<div style='margin-top: 30px;'>";
echo "<h3>🔗 Liens utiles:</h3>";
echo "<a href='login.php' class='btn'>🔐 Accéder à l'administration</a>";
echo "<a href='setup.php' class='btn'>🚀 Réessayer l'installation</a>";
echo "<a href='../index.html' class='btn'>🏠 Retour au site</a>";
echo "</div>";

echo "</body>";
echo "</html>";
?>

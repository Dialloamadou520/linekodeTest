<?php
// Script d'installation pour serveur web - Linekode Admin
echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Installation Serveur Web - Linekode Admin</title>";
echo "    <style>";
echo "        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }";
echo "        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 10px 0; }";
echo "        .btn { background: #0284c7; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }";
echo "        .btn:hover { background: #0369a1; }";
echo "        .btn-success { background: #28a745; }";
echo "        .btn-success:hover { background: #218838; }";
echo "        code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }";
echo "        pre { background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto; }";
echo "        .step { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 8px; }";
echo "        .step h3 { margin-top: 0; color: #0284c7; }";
echo "    </style>";
echo "</head>";
echo "<body>";

echo "<h1>🖥️ Installation Linekode Admin - Serveur Web</h1>";

// Vérifier si MySQL est disponible
if (!extension_loaded('pdo_mysql')) {
    echo "<div class='error'>";
    echo "<h3>❌ Extension PDO MySQL non activée</h3>";
    echo "<p>Veuillez activer l'extension PDO MySQL dans votre configuration PHP.</p>";
    echo "<p>Contactez votre hébergeur si vous ne pouvez pas modifier php.ini</p>";
    echo "</div>";
    echo "</body></html>";
    exit;
}

// Afficher les informations du serveur
echo "<div class='warning'>";
echo "<h3>📊 Informations du Serveur Détectées:</h3>";
echo "<p><strong>Nom du serveur:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'Inconnu') . "</p>";
echo "<p><strong>IP du serveur:</strong> " . ($_SERVER['SERVER_ADDR'] ?? 'Inconnue') . "</p>";
echo "<p><strong>Version PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Extension PDO MySQL:</strong> " . (extension_loaded('pdo_mysql') ? '✅ Activée' : '❌ Non activée') . "</p>";
echo "<p><strong>Version MySQL:</strong> ";
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo $version . " ✅";
} catch (Exception $e) {
    echo "Non détectée ❌";
}
echo "</p>";
echo "</div>";

// Essayer de se connecter et installer automatiquement
$installationSuccess = false;

// Liste des utilisateurs à essayer
$usersToTry = [
    ['linekode_admin', 'linekode2024'],
    ['root', ''],
    ['root', 'root'],
    ['linekode', 'linekode2024'],
    [getDomainUser(), 'linekode2024'],
    [getDomainUser(), ''],
];

foreach ($usersToTry as $user) {
    try {
        $pdo = new PDO('mysql:host=localhost', $user[0], $user[1]);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='success'>";
        echo "<h3>✅ Connexion réussie avec l'utilisateur: " . $user[0] . "</h3>";
        echo "</div>";
        
        // Tenter l'installation
        if ($user[0] !== 'linekode_admin') {
            // Créer l'utilisateur linekode_admin
            $pdo->exec("CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024'");
            $pdo->exec("GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost'");
            $pdo->exec("FLUSH PRIVILEGES");
            echo "<div class='success'>";
            echo "<h3>✅ Utilisateur linekode_admin créé avec succès</h3>";
            echo "</div>";
        }
        
        // Se connecter avec linekode_admin
        $pdo = new PDO('mysql:host=localhost;dbname=linekode_admin', 'linekode_admin', 'linekode2024');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Créer les tables
        createTables($pdo);
        
        // Insérer les données initiales
        insertInitialData($pdo);
        
        $installationSuccess = true;
        break;
        
    } catch (PDOException $e) {
        echo "<div class='info'>";
        echo "<h3>⚠️ Échec avec l'utilisateur: " . $user[0] . "</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

if ($installationSuccess) {
    echo "<div class='success'>";
    echo "<h2>🎉 Installation terminée avec succès!</h2>";
    echo "<p><strong>Base de données:</strong> linekode_admin</p>";
    echo "<p><strong>Utilisateur:</strong> linekode_admin</p>";
    echo "<p><strong>Mot de passe:</strong> linekode2024</p>";
    echo "</div>";
    
    echo "<div class='success'>";
    echo "<h3>🔗 Prochaines étapes:</h3>";
    echo "<ol>";
    echo "<li>Accédez à l'administration: <a href='login.php'>login.php</a></li>";
    echo "<li>Identifiants: admin / linekode2024</li>";
    echo "<li>Commencez à gérer vos inscriptions et messages</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='margin-top: 30px;'>";
    echo "<a href='login.php' class='btn btn-success'>🔐 Accéder à l'administration</a>";
    echo "<a href='../index.html' class='btn'>🏠 Retour au site</a>";
    echo "</div>";
    
} else {
    echo "<div class='error'>";
    echo "<h2>❌ Installation automatique échouée</h2>";
    echo "<p>Veuillez configurer manuellement votre base de données MySQL.</p>";
    echo "</div>";
    
    displayManualInstructions();
}

echo "</body>";
echo "</html>";

function getDomainUser() {
    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $domainParts = explode('.', $serverName);
    return $domainParts[0] ?? 'linekode';
}

function createTables($pdo) {
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
}

function insertInitialData($pdo) {
    // Créer l'utilisateur admin par défaut
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute(['admin', password_hash('linekode2024', PASSWORD_DEFAULT), 'admin@linekode.sn']);
    
    // Insérer les paramètres par défaut
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
    
    // Insérer une annonce de bienvenue
    $stmt = $pdo->prepare("INSERT IGNORE INTO annonces (title, content, status, date, author) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        'Bienvenue sur Linekode',
        'Nous sommes ravis de vous accueillir dans notre école de formation en développement web.',
        'published',
        date('Y-m-d'),
        'Admin'
    ]);
    
    echo "<div class='success'>";
    echo "<h3>✅ Données initiales insérées</h3>";
    echo "</div>";
}

function displayManualInstructions() {
    echo "<div class='step'>";
    echo "<h3>📋 Étape 1: Via votre panneau de contrôle</h3>";
    echo "<p>Connectez-vous à votre panneau d'hébergement (cPanel, Plesk, DirectAdmin, etc.)</p>";
    echo "<ol>";
    echo "<li>Cherchez 'MySQL Databases' ou 'Base de données MySQL'</li>";
    echo "<li>Créez une base de données nommée: <code>linekode_admin</code></li>";
    echo "<li>Créez un utilisateur: <code>linekode_admin</code></li>";
    echo "<li>Donnez tous les privilèges sur la base de données</li>";
    echo "<li>Notez le mot de passe</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Étape 2: Via phpMyAdmin</h3>";
    echo "<ol>";
    echo "<li>Accédez à phpMyAdmin via votre hébergeur</li>";
    echo "<li>Cliquez sur 'Nouvelle base de données'</li>";
    echo "<li>Nommez-la: <code>linekode_admin</code></li>";
    echo "<li>Cliquez sur 'Utilisateurs'</li>";
    echo "<li>Ajoutez un utilisateur: <code>linekode_admin</code></li>";
    echo "<li>Cochez 'Donner tous les privilèges'</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Étape 3: Importation du script SQL</h3>";
    echo "<p>Une fois la base de données créée, importez le fichier <code>database-setup.sql</code></p>";
    echo "<ol>";
    echo "<li>Dans phpMyAdmin, sélectionnez la base <code>linekode_admin</code></li>";
    echo "<li>Cliquez sur 'Importer'</li>";
    echo "<li>Sélectionnez le fichier <code>database-setup.sql</code></li>";
    echo "<li>Cliquez sur 'Exécuter'</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>📋 Étape 4: Modification du fichier de configuration</h3>";
    echo "<p>Si vous utilisez des identifiants différents, modifiez <code>config-server.php</code>:</p>";
    echo "<pre>";
    echo "private \$username = 'votre_utilisateur_mysql';";
    echo "private \$password = 'votre_mot_de_passe_mysql';";
    echo "</pre>";
    echo "</div>";
    
    echo "<div style='margin-top: 30px;'>";
    echo "<a href='setup-server.php' class='btn'>🔄 Réessayer l'installation</a>";
    echo "<a href='login.php' class='btn'>🔐 Accès admin</a>";
    echo "<a href='../index.html' class='btn'>🏠 Retour au site</a>";
    echo "</div>";
}
?>

<?php
// Configuration de la base de données - Linekode Admin (Version corrigée)
class Database {
    private $host = 'localhost';
    private $dbname = 'linekode_admin';
    private $username = 'linekode_admin';
    private $password = 'linekode2024';
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            // Si la connexion échoue, essayer avec root sans mot de passe
            $this->tryAlternativeConnection();
        }
    }

    private function tryAlternativeConnection() {
        try {
            // Essayer avec root sans mot de passe
            $this->conn = new PDO(
                "mysql:host=localhost",
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Créer la base de données et l'utilisateur
            $this->setupDatabase();
            
            // Se reconnecter avec le nouvel utilisateur
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
        } catch(PDOException $e) {
            // Si ça échoue encore, afficher un message d'aide
            $this->displayConnectionHelp($e);
        }
    }

    private function setupDatabase() {
        try {
            // Créer la base de données si elle n'existe pas
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS linekode_admin");
            
            // Créer l'utilisateur si nécessaire
            $this->conn->exec("CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024'");
            $this->conn->exec("GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost'");
            $this->conn->exec("FLUSH PRIVILEGES");
            
            // Sélectionner la base de données
            $this->conn->exec("USE linekode_admin");
            
            // Créer les tables
            $this->createTables();
            
        } catch(PDOException $e) {
            throw new Exception("Erreur lors de la création de la base de données: " . $e->getMessage());
        }
    }

    private function createTables() {
        $sql = [
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

        foreach ($sql as $query) {
            $this->conn->exec($query);
        }

        // Insérer les données initiales
        $this->insertInitialData();
    }

    private function insertInitialData() {
        // Créer l'utilisateur admin par défaut
        $stmt = $this->conn->prepare("INSERT IGNORE INTO admin_users (username, password, email) VALUES (?, ?, ?)");
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

        $stmt = $this->conn->prepare("INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?)");
        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value, "Paramètre: $key"]);
        }

        // Insérer une annonce de bienvenue
        $stmt = $this->conn->prepare("INSERT IGNORE INTO annonces (title, content, status, date, author) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            'Bienvenue sur Linekode',
            'Nous sommes ravis de vous accueillir dans notre école de formation en développement web.',
            'published',
            date('Y-m-d'),
            'Admin'
        ]);
    }

    private function displayConnectionHelp($error) {
        echo "<!DOCTYPE html>";
        echo "<html lang='fr'>";
        echo "<head>";
        echo "    <meta charset='UTF-8'>";
        echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "    <title>Erreur de connexion MySQL</title>";
        echo "    <style>";
        echo "        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }";
        echo "        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0; }";
        echo "        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0; }";
        echo "        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; }";
        echo "        .btn { background: #0284c7; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }";
        echo "        .btn:hover { background: #0369a1; }";
        echo "        code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }";
        echo "    </style>";
        echo "</head>";
        echo "<body>";
        
        echo "<h1>🔧 Erreur de connexion MySQL</h1>";
        echo "<div class='error'>";
        echo "<strong>Erreur:</strong> " . $error->getMessage();
        echo "</div>";
        
        echo "<h2>🛠️ Solutions possibles:</h2>";
        
        echo "<div class='info'>";
        echo "<h3>Option 1: Utiliser XAMPP avec MySQL</h3>";
        echo "<ol>";
        echo "<li>Démarrez XAMPP Control Panel</li>";
        echo "<li>Démarrez le service MySQL</li>";
        echo "<li>Accédez à phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>Cliquez sur l'onglet 'SQL'</li>";
        echo "<li>Collez et exécutez le contenu du fichier <code>database-setup.sql</code></li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<h3>Option 2: Créer manuellement la base de données</h3>";
        echo "<p>Exécutez ces commandes dans MySQL:</p>";
        echo "<pre>";
        echo "CREATE DATABASE linekode_admin;";
        echo "CREATE USER 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';";
        echo "GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost';";
        echo "FLUSH PRIVILEGES;";
        echo "</pre>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<h3>Option 3: Modifier les identifiants MySQL</h3>";
        echo "<p>Si vous avez déjà un utilisateur MySQL, modifiez le fichier <code>config-fixed.php</code>:</p>";
        echo "<pre>";
        echo "private \$username = 'votre_utilisateur';";
        echo "private \$password = 'votre_mot_de_passe';";
        echo "</pre>";
        echo "</div>";
        
        echo "<div class='success'>";
        echo "<h3>✅ Une fois la base de données créée:</h3>";
        echo "<ol>";
        echo "<li>Rafraîchissez cette page</li>";
        echo "<li>Accédez à l'administration: <a href='login.php'>login.php</a></li>";
        echo "<li>Identifiants: admin / linekode2024</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div style='margin-top: 30px;'>";
        echo "<a href='setup.php' class='btn'>🚀 Réessayer l'installation</a>";
        echo "<a href='login.php' class='btn'>🔐 Accès admin</a>";
        echo "<a href='../index.html' class='btn'>🏠 Retour au site</a>";
        echo "</div>";
        
        echo "</body>";
        echo "</html>";
        exit;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function setupDatabaseAndTables() {
        $this->setupDatabase();
    }
}

// Fonctions utilitaires
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^(\+221)?[234567]\d{8}$/', preg_replace('/\s/', '', $phone));
}

function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function sendEmail($to, $subject, $content) {
    // Configuration email (à adapter selon votre serveur)
    $headers = [
        'From: noreply@linekode.sn',
        'Reply-To: admin@linekode.sn',
        'Content-Type: text/html; charset=UTF-8'
    ];
    
    return mail($to, $subject, $content, $headers);
}

// Session management
function startSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Initialiser la base de données
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    // L'erreur sera affichée par displayConnectionHelp()
    exit;
}
?>

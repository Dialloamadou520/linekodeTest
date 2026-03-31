-- Script de configuration pour la base de données Linekode Admin
-- Exécutez ce script dans MySQL pour créer la base de données et l'utilisateur

-- 1. Créer la base de données
CREATE DATABASE IF NOT EXISTS linekode_admin;
USE linekode_admin;

-- 2. Créer l'utilisateur admin avec mot de passe
CREATE USER IF NOT EXISTS 'linekode_admin'@'localhost' IDENTIFIED BY 'linekode2024';
GRANT ALL PRIVILEGES ON linekode_admin.* TO 'linekode_admin'@'localhost';
FLUSH PRIVILEGES;

-- 3. Donner les permissions à l'utilisateur si besoin
-- GRANT ALL PRIVILEGES ON *.* TO 'linekode_admin'@'%';

-- 4. Créer les tables
CREATE TABLE IF NOT EXISTS inscriptions (
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
);

CREATE TABLE IF NOT EXISTS annonces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    status ENUM('draft', 'published', 'scheduled') DEFAULT 'draft',
    date DATE,
    author VARCHAR(100) DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
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
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    last_login TIMESTAMP NULL,
    remember_token VARCHAR(255) NULL,
    token_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 5. Insérer les données initiales
INSERT IGNORE INTO admin_users (username, password, email) VALUES 
    ('admin', '$2y$10$92Vx0W3K9aL5q7nB6qD5s2E5J6Qw5J2n5J6Qw5', 'admin@linekode.sn');

INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES 
    ('site_name', 'Linekode', 'Nom du site'),
    ('admin_email', 'admin@linekode.sn', 'Email de l\'administrateur'),
    ('currency', 'FCFA', 'Devise monnaie'),
    ('company_name', 'Linekode Sénégal', 'Nom de l\'entreprise'),
    ('company_phone', '+221 71 117 93 93', 'Téléphone'),
    ('company_address', 'Dakar, Sénégal', 'Adresse');

INSERT IGNORE INTO annonces (title, content, status, date, author) VALUES 
    ('Bienvenue sur Linekode', 'Nous sommes ravis de vous accueillir dans notre école de formation en développement web.', 'published', CURDATE(), 'Admin');

-- 5.1. Table pour les formations
CREATE TABLE IF NOT EXISTS formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    duration VARCHAR(50),
    price VARCHAR(50),
    level ENUM('debutant', 'intermediaire', 'avance') DEFAULT 'debutant',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des paiements (DexpayAfrica)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) UNIQUE NOT NULL,
    dexpay_transaction_id VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'XOF',
    method ENUM('mobile_money', 'card', 'bank_transfer') NOT NULL DEFAULT 'mobile_money',
    status ENUM('pending', 'completed', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    inscription_id INT,
    description TEXT,
    phone_number VARCHAR(20),
    email VARCHAR(255),
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) ON DELETE SET NULL,
    INDEX idx_session_id (session_id),
    INDEX idx_dexpay_transaction_id (dexpay_transaction_id),
    INDEX idx_status (status),
    INDEX idx_method (method),
    INDEX idx_phone_number (phone_number),
    INDEX idx_created_at (created_at)
);

-- Insérer des formations par défaut
INSERT IGNORE INTO formations (title, description, duration, price, level, status) VALUES 
('Développement Web Frontend', 'Apprenez HTML, CSS, JavaScript et les frameworks modernes comme React et Vue.js pour créer des interfaces web interactives et responsives.', '3 mois', '50 000 FCFA', 'debutant', 'active'),
('Développement Web Backend', 'Maîtrisez PHP, MySQL, Node.js et les API REST pour créer des applications web robustes et performantes.', '3 mois', '50 000 FCFA', 'intermediaire', 'active'),
('Applications Mobiles', 'Développez des applications natives et hybrides pour iOS et Android avec React Native et Flutter.', '4 mois', '75 000 FCFA', 'intermediaire', 'active'),
('UI/UX Design', 'Apprenez les principes du design d\'interface et d\'expérience utilisateur avec Figma, Adobe XD et les meilleures pratiques.', '2 mois', '40 000 FCFA', 'debutant', 'active'),
('Base de Données Avancées', 'Approfondissez vos connaissances en SQL, NoSQL, optimisation et administration de bases de données.', '2 mois', '45 000 FCFA', 'avance', 'active');

-- 6. Créer les index pour optimiser les performances
CREATE INDEX IF NOT EXISTS idx_inscriptions_status ON inscriptions(status);
CREATE INDEX IF NOT EXISTS idx_inscriptions_date ON inscriptions(date);
CREATE INDEX IF NOT EXISTS idx_messages_read_status ON messages(read_status);
CREATE INDEX IF NOT EXISTS idx_messages_date ON messages(date);
CREATE INDEX IF NOT EXISTS idx_annonces_status ON annonces(status);
CREATE INDEX IF NOT EXISTS idx_annonces_date ON annonces(date);
CREATE INDEX IF NOT EXISTS idx_formations_status ON formations(status);
CREATE INDEX IF NOT EXISTS idx_formations_level ON formations(level);
CREATE INDEX IF NOT EXISTS idx_formations_created_at ON formations(created_at);

-- 7. Afficher les informations de configuration
SELECT 'Database: linekode_admin' as db_info,
       'User: linekode_admin@localhost' as user_info,
       'Tables créées: 5' as tables_count,
       'Permissions: GRANTED' as permissions;

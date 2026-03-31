-- Table pour les formations
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

-- Insérer des formations par défaut
INSERT IGNORE INTO formations (title, description, duration, price, level, status) VALUES 
('Développement Web Frontend', 'Apprenez HTML, CSS, JavaScript et les frameworks modernes comme React et Vue.js pour créer des interfaces web interactives et responsives.', '3 mois', '50 000 FCFA', 'debutant', 'active'),
('Développement Web Backend', 'Maîtrisez PHP, MySQL, Node.js et les API REST pour créer des applications web robustes et performantes.', '3 mois', '50 000 FCFA', 'intermediaire', 'active'),
('Applications Mobiles', 'Développez des applications natives et hybrides pour iOS et Android avec React Native et Flutter.', '4 mois', '75 000 FCFA', 'intermediaire', 'active'),
('UI/UX Design', 'Apprenez les principes du design d\'interface et d\'expérience utilisateur avec Figma, Adobe XD et les meilleures pratiques.', '2 mois', '40 000 FCFA', 'debutant', 'active'),
('Base de Données Avancées', 'Approfondissez vos connaissances en SQL, NoSQL, optimisation et administration de bases de données.', '2 mois', '45 000 FCFA', 'avance', 'active');

-- Créer des index pour optimiser les performances
CREATE INDEX IF NOT EXISTS idx_formations_status ON formations(status);
CREATE INDEX IF NOT EXISTS idx_formations_level ON formations(level);
CREATE INDEX IF NOT EXISTS idx_formations_created_at ON formations(created_at);

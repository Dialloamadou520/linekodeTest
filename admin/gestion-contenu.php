<?php
require_once 'config-server.php';
require_once 'auth-check.php';
requireLogin();

// Traitement des formulaires
$message = '';
$message_type = '';

// Ajouter/Modifier une annonce
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add_annonce' || $action === 'edit_annonce') {
        $title = sanitizeInput($_POST['title'] ?? '');
        $content = sanitizeInput($_POST['content'] ?? '');
        $status = sanitizeInput($_POST['status'] ?? 'draft');
        $date = $_POST['date'] ?? date('Y-m-d');
        $author = sanitizeInput($_POST['author'] ?? 'Admin');
        $id = $_POST['id'] ?? '';
        
        if (empty($title) || empty($content)) {
            $message = 'Veuillez remplir tous les champs obligatoires';
            $message_type = 'error';
        } else {
            try {
                if ($action === 'add_annonce') {
                    $stmt = $db->prepare("INSERT INTO annonces (title, content, status, date, author) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $content, $status, $date, $author]);
                    $message = 'Annonce ajoutée avec succès';
                } else {
                    $stmt = $db->prepare("UPDATE annonces SET title = ?, content = ?, status = ?, date = ?, author = ? WHERE id = ?");
                    $stmt->execute([$title, $content, $status, $date, $author, $id]);
                    $message = 'Annonce mise à jour avec succès';
                }
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Erreur: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    
    // Supprimer une annonce
    elseif ($action === 'delete_annonce') {
        $id = $_POST['id'] ?? '';
        if (!empty($id)) {
            try {
                $stmt = $db->prepare("DELETE FROM annonces WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Annonce supprimée avec succès';
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Erreur: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    
    // Ajouter/Modifier une formation
    elseif ($action === 'add_formation' || $action === 'edit_formation') {
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $duration = sanitizeInput($_POST['duration'] ?? '');
        $price = sanitizeInput($_POST['price'] ?? '');
        $level = sanitizeInput($_POST['level'] ?? '');
        $status = sanitizeInput($_POST['status'] ?? 'active');
        $id = $_POST['id'] ?? '';
        
        if (empty($title) || empty($description)) {
            $message = 'Veuillez remplir tous les champs obligatoires';
            $message_type = 'error';
        } else {
            try {
                if ($action === 'add_formation') {
                    $stmt = $db->prepare("INSERT INTO formations (title, description, duration, price, level, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$title, $description, $duration, $price, $level, $status]);
                    $message = 'Formation ajoutée avec succès';
                } else {
                    $stmt = $db->prepare("UPDATE formations SET title = ?, description = ?, duration = ?, price = ?, level = ?, status = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $duration, $price, $level, $status, $id]);
                    $message = 'Formation mise à jour avec succès';
                }
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Erreur: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    
    // Supprimer une formation
    elseif ($action === 'delete_formation') {
        $id = $_POST['id'] ?? '';
        if (!empty($id)) {
            try {
                $stmt = $db->prepare("DELETE FROM formations WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Formation supprimée avec succès';
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Erreur: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Récupérer les données
$annonces = $db->query("SELECT * FROM annonces ORDER BY created_at DESC")->fetchAll();
$formations = $db->query("SELECT * FROM formations ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Contenu - Linekode Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        .admin-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .admin-nav button {
            background: #0284c7;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        .admin-nav button:hover {
            background: #0369a1;
        }
        .admin-nav button.active {
            background: #28a745;
        }
        .content-section {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .content-section.active {
            display: block;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .btn {
            background: #0284c7;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #0369a1;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background: #e0a800;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .data-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .data-table tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-published {
            background: #d4edda;
            color: #155724;
        }
        .status-draft {
            background: #fff3cd;
            color: #856404;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .action-buttons button {
            padding: 6px 12px;
            font-size: 12px;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            margin: 50px auto;
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .admin-nav {
                flex-direction: column;
            }
            .data-table {
                font-size: 14px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>📝 Gestion du Contenu</h1>
            <p>Ajoutez et modifiez les annonces, formations et autres contenus sans toucher au code</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <nav class="admin-nav">
            <button class="active" onclick="showSection('annonces')">📢 Annonces</button>
            <button onclick="showSection('formations')">🎓 Formations</button>
            <button onclick="showSection('settings')">⚙️ Paramètres</button>
            <button onclick="showSection('pages')">📄 Pages</button>
        </nav>

        <!-- Section Annonces -->
        <section id="annonces" class="content-section active">
            <h2>📢 Gestion des Annonces</h2>
            
            <form method="POST" style="margin-bottom: 30px;">
                <input type="hidden" name="action" value="add_annonce">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Titre de l'annonce *</label>
                        <input type="text" id="title" name="title" required placeholder="Ex: Nouvelle formation disponible">
                    </div>
                    <div class="form-group">
                        <label for="status">Statut</label>
                        <select id="status" name="status">
                            <option value="draft">Brouillon</option>
                            <option value="published">Publié</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="content">Contenu de l'annonce *</label>
                    <textarea id="content" name="content" required placeholder="Décrivez votre annonce en détail..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="author">Auteur</label>
                        <input type="text" id="author" name="author" value="Admin">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i> Ajouter l'annonce
                </button>
            </form>

            <h3>Annonces existantes</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Auteur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($annonces as $annonce): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($annonce['title']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $annonce['status']; ?>">
                                    <?php echo $annonce['status'] === 'published' ? 'Publié' : 'Brouillon'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($annonce['date'])); ?></td>
                            <td><?php echo htmlspecialchars($annonce['author']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-warning" onclick="editAnnonce(<?php echo $annonce['id']; ?>)">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteAnnonce(<?php echo $annonce['id']; ?>)">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Section Formations -->
        <section id="formations" class="content-section">
            <h2>🎓 Gestion des Formations</h2>
            
            <form method="POST" style="margin-bottom: 30px;">
                <input type="hidden" name="action" value="add_formation">
                
                <div class="form-group">
                    <label for="formation_title">Titre de la formation *</label>
                    <input type="text" id="formation_title" name="title" required placeholder="Ex: Développement Web Frontend">
                </div>
                
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required placeholder="Décrivez la formation en détail..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="duration">Durée</label>
                        <input type="text" id="duration" name="duration" placeholder="Ex: 3 mois">
                    </div>
                    <div class="form-group">
                        <label for="price">Prix</label>
                        <input type="text" id="price" name="price" placeholder="Ex: 50 000 FCFA">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="level">Niveau</label>
                        <select id="level" name="level">
                            <option value="debutant">Débutant</option>
                            <option value="intermediaire">Intermédiaire</option>
                            <option value="avance">Avancé</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="formation_status">Statut</label>
                        <select id="formation_status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i> Ajouter la formation
                </button>
            </form>

            <h3>Formations existantes</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Durée</th>
                        <th>Prix</th>
                        <th>Niveau</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formations as $formation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($formation['title']); ?></td>
                            <td><?php echo htmlspecialchars($formation['duration']); ?></td>
                            <td><?php echo htmlspecialchars($formation['price']); ?></td>
                            <td><?php echo htmlspecialchars($formation['level']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $formation['status']; ?>">
                                    <?php echo $formation['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-warning" onclick="editFormation(<?php echo $formation['id']; ?>)">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteFormation(<?php echo $formation['id']; ?>)">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Section Paramètres -->
        <section id="settings" class="content-section">
            <h2>⚙️ Paramètres du Site</h2>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="form-group">
                    <label for="site_name">Nom du site</label>
                    <input type="text" id="site_name" name="site_name" value="Linekode">
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Email administrateur</label>
                    <input type="email" id="admin_email" name="admin_email" value="admin@linekode.sn">
                </div>
                
                <div class="form-group">
                    <label for="company_phone">Téléphone</label>
                    <input type="text" id="company_phone" name="company_phone" value="+221 71 117 93 93">
                </div>
                
                <div class="form-group">
                    <label for="company_address">Adresse</label>
                    <input type="text" id="company_address" name="company_address" value="Saint-Louis, Sénégal">
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Sauvegarder les paramètres
                </button>
            </form>
        </section>

        <!-- Section Pages -->
        <section id="pages" class="content-section">
            <h2>📄 Gestion des Pages</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <h3>🏠 Page d'accueil</h3>
                    <p>Modifier le contenu de la page d'accueil</p>
                    <button class="btn" onclick="editPage('home')">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                </div>
                
                <div class="form-group">
                    <h3>📚 Formations</h3>
                    <p>Gérer les formations et les cours</p>
                    <button class="btn" onclick="editPage('formations')">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                </div>
                
                <div class="form-group">
                    <h3>👥 À propos</h3>
                    <p>Modifier la page à propos de nous</p>
                    <button class="btn" onclick="editPage('about')">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                </div>
                
                <div class="form-group">
                    <h3>📞 Contact</h3>
                    <p>Modifier les informations de contact</p>
                    <button class="btn" onclick="editPage('contact')">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                </div>
            </div>
        </section>
    </div>

    <!-- Modals -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h3 id="modalTitle">Modifier</h3>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Cacher toutes les sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Retirer la classe active de tous les boutons
            document.querySelectorAll('.admin-nav button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Afficher la section sélectionnée
            document.getElementById(sectionId).classList.add('active');
            
            // Ajouter la classe active au bouton cliqué
            event.target.classList.add('active');
        }

        function editAnnonce(id) {
            // Récupérer les données de l'annonce
            fetch(`api/annonces.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    const modalBody = `
                        <form method="POST">
                            <input type="hidden" name="action" value="edit_annonce">
                            <input type="hidden" name="id" value="${data.id}">
                            
                            <div class="form-group">
                                <label for="edit_title">Titre</label>
                                <input type="text" id="edit_title" name="title" value="${data.title}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_content">Contenu</label>
                                <textarea id="edit_content" name="content" required>${data.content}</textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit_status">Statut</label>
                                    <select id="edit_status" name="status">
                                        <option value="draft" ${data.status === 'draft' ? 'selected' : ''}>Brouillon</option>
                                        <option value="published" ${data.status === 'published' ? 'selected' : ''}>Publié</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_date">Date</label>
                                    <input type="date" id="edit_date" name="date" value="${data.date}">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Sauvegarder
                            </button>
                        </form>
                    `;
                    
                    document.getElementById('modalTitle').textContent = 'Modifier l\'annonce';
                    document.getElementById('modalBody').innerHTML = modalBody;
                    document.getElementById('editModal').style.display = 'block';
                });
        }

        function deleteAnnonce(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_annonce">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editFormation(id) {
            // Implémenter l'édition des formations
            alert('Fonctionnalité d\'édition des formations à implémenter');
        }

        function deleteFormation(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_formation">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editPage(pageType) {
            alert(`Édition de la page ${pageType} à implémenter`);
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Fermer le modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>

<?php
require_once 'config-server.php';
require_once 'auth-check.php';
requireLogin();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Administration - Linekode</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-menu-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 40px;
        }
        .admin-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .admin-header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .menu-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        }
        .menu-card-icon {
            font-size: 3em;
            margin-bottom: 20px;
            color: #0284c7;
        }
        .menu-card h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #333;
        }
        .menu-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .menu-card-btn {
            background: #0284c7;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .menu-card-btn:hover {
            background: #0369a1;
        }
        .stats-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .quick-actions h3 {
            margin-bottom: 20px;
            color: #333;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .action-btn {
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .action-btn:hover {
            background: #218838;
        }
        .action-btn.danger {
            background: #dc3545;
        }
        .action-btn.danger:hover {
            background: #c82333;
        }
        .action-btn.warning {
            background: #ffc107;
            color: #212529;
        }
        .action-btn.warning:hover {
            background: #e0a800;
        }
        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-menu-container">
        <div class="admin-header">
            <h1>🎛️ Panneau d'Administration</h1>
            <p>Gérez votre site Linekode facilement sans toucher au code</p>
        </div>

        <!-- Statistiques -->
        <div class="stats-section">
            <h3>📊 Statistiques en temps réel</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="inscriptionsCount">0</div>
                    <div class="stat-label">Inscriptions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="messagesCount">0</div>
                    <div class="stat-label">Messages</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="annoncesCount">0</div>
                    <div class="stat-label">Annonces</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="formationsCount">0</div>
                    <div class="stat-label">Formations</div>
                </div>
            </div>
        </div>

        <!-- Menu principal -->
        <div class="menu-grid">
            <a href="dashboard.php" class="menu-card">
                <div class="menu-card-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h3>📊 Dashboard</h3>
                <p>Vue d'ensemble des statistiques et activités récentes</p>
                <span class="menu-card-btn">Accéder</span>
            </a>

            <a href="gestion-contenu.php" class="menu-card">
                <div class="menu-card-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <h3>📝 Gestion du Contenu</h3>
                <p>Ajoutez et modifiez annonces, formations et pages sans code</p>
                <span class="menu-card-btn">Gérer</span>
            </a>

            <a href="inscriptions.html" class="menu-card">
                <div class="menu-card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>👥 Inscriptions</h3>
                <p>Gérez les inscriptions des étudiants et suivez leur progression</p>
                <span class="menu-card-btn">Voir</span>
            </a>

            <a href="messages.html" class="menu-card">
                <div class="menu-card-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>💬 Messages</h3>
                <p>Lisez et répondez aux messages des visiteurs</p>
                <span class="menu-card-btn">Consulter</span>
            </a>

            <a href="annonces.html" class="menu-card">
                <div class="menu-card-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3>📢 Annonces</h3>
                <p>Publiez et gérez les annonces importantes</p>
                <span class="menu-card-btn">Gérer</span>
            </a>

            <a href="statistiques.html" class="menu-card">
                <div class="menu-card-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>📈 Statistiques</h3>
                <p>Analyse détaillée des performances et tendances</p>
                <span class="menu-card-btn">Analyser</span>
            </a>
        </div>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <h3>⚡ Actions Rapides</h3>
            <div class="quick-actions">
                <a href="gestion-contenu.php" class="quick-action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouveau contenu</span>
                </a>
                <a href="inscriptions.html" class="quick-action-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>Nouvelle inscription</span>
                </a>
                <a href="payments-management.php" class="quick-action-btn">
                    <i class="fas fa-credit-card"></i>
                    <span>Voir paiements</span>
                </a>
                <a href="messages.html" class="quick-action-btn">
                    <i class="fas fa-envelope"></i>
                    <span>Messagerie</span>
                </a>
            </div>
            <div class="action-buttons">
                <a href="#" class="action-btn warning" onclick="exportData()">
                    <i class="fas fa-download"></i> Exporter
                </a>
                <a href="#" class="action-btn danger" onclick="confirmReset()">
                    <i class="fas fa-trash"></i> Réinitialiser
                </a>
                <a href="logout.php" class="action-btn">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>

    <script>
        // Charger les statistiques
        function loadStats() {
            fetch('api/dashboard.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('inscriptionsCount').textContent = data.total_inscriptions || 0;
                    document.getElementById('messagesCount').textContent = data.total_messages || 0;
                    document.getElementById('annoncesCount').textContent = data.total_annonces || 0;
                    document.getElementById('formationsCount').textContent = data.total_formations || 0;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des statistiques:', error);
                    // Valeurs par défaut
                    document.getElementById('inscriptionsCount').textContent = '0';
                    document.getElementById('messagesCount').textContent = '0';
                    document.getElementById('annoncesCount').textContent = '0';
                    document.getElementById('formationsCount').textContent = '0';
                });
        }

        // Exporter les données
        function exportData() {
            if (confirm('Voulez-vous exporter toutes les données ?')) {
                // Implémenter l'exportation
                alert('Fonctionnalité d\'exportation à implémenter');
            }
        }

        // Confirmer la réinitialisation
        function confirmReset() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les données ? Cette action est irréversible !')) {
                if (confirm('Dernière confirmation : cette action supprimera toutes les inscriptions, messages et annonces.')) {
                    // Implémenter la réinitialisation
                    alert('Fonctionnalité de réinitialisation à implémenter');
                }
            }
        }

        // Charger les statistiques au chargement de la page
        document.addEventListener('DOMContentLoaded', loadStats);

        // Rafraîchir les statistiques toutes les 30 secondes
        setInterval(loadStats, 30000);
    </script>
</body>
</html>

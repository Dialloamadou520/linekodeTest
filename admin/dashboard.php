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
    <title>Dashboard Admin - Linekode</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-dashboard {
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Arial', sans-serif;
        }
        .admin-header {
            background: #0284c7;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: bold;
        }
        .admin-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }
        .admin-container {
            display: flex;
            min-height: calc(100vh - 70px);
        }
        .admin-sidebar {
            width: 250px;
            background: white;
            border-right: 1px solid #e5e7eb;
            padding: 20px 0;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #f0f9ff;
            color: #0284c7;
            border-right: 3px solid #0284c7;
        }
        .admin-content {
            flex: 1;
            padding: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-left: 4px solid #0284c7;
        }
        .stat-card h3 {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .stat-change {
            font-size: 12px;
            color: #10b981;
        }
        .content-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }
        .btn-primary {
            background: #0284c7;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn-primary:hover {
            background: #0369a1;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            background: #f8fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-new {
            background: #dcfce7;
            color: #16a34a;
        }
        .badge-pending {
            background: #fef3c7;
            color: #d97706;
        }
        .badge-confirmed {
            background: #dbeafe;
            color: #2563eb;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <!-- Header -->
        <header class="admin-header">
            <div class="admin-logo">
                <i class="fas fa-code"></i>
                <span>Linekode Admin</span>
            </div>
            <div class="admin-user">
                <span><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['admin_user']['username']) ?></span>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </header>

        <div class="admin-container">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <ul class="sidebar-menu">
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="inscriptions.php"><i class="fas fa-user-plus"></i> Inscriptions</a></li>
                    <li><a href="annonces.php"><i class="fas fa-bullhorn"></i> Annonces</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="admin-content">
                <div id="dashboardContent">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin" style="font-size: 48px;"></i>
                        <p>Chargement du dashboard...</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Charger les données du dashboard
        async function loadDashboardData() {
            try {
                const response = await fetch('api/dashboard.php?action=stats');
                const data = await response.json();
                
                if (data.success) {
                    updateStatCards(data.data);
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur de chargement des données', 'error');
            }
        }

        function updateStatCards(stats) {
            const cards = [
                { id: 'totalInscriptions', value: stats.totalInscriptions },
                { id: 'newInscriptions', value: stats.newInscriptions },
                { id: 'totalAnnonces', value: stats.totalAnnonces },
                { id: 'unreadMessages', value: stats.unreadMessages }
            ];

            cards.forEach(card => {
                const element = document.querySelector(`[data-stat="${card.id}"]`);
                if (element) {
                    animateNumber(element, 0, card.value);
                }
            });
        }

        function animateNumber(element, start, end) {
            const duration = 1000;
            const startTime = performance.now();
            
            function animate(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = Math.floor(start + (end - start) * progress);
                
                element.textContent = current.toLocaleString();
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }
            
            requestAnimationFrame(animate);
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i> ${message}`;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Charger les données au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            
            // Rafraîchir toutes les 30 secondes
            setInterval(loadDashboardData, 30000);
        });
    </script>
</body>
</html>

<?php
require_once 'config-server.php';
require_once 'auth-check.php';
requireLogin();

// Page de gestion des paiements pour l'administration
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Paiements - Linekode Admin</title>
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .payments-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .payments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .payments-title {
            font-size: 1.8rem;
            color: #333;
            margin: 0;
        }

        .payments-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .stat-card.revenue .stat-value {
            color: #28a745;
        }

        .stat-card.pending .stat-value {
            color: #ffc107;
        }

        .stat-card.completed .stat-value {
            color: #007bff;
        }

        .stat-card.failed .stat-value {
            color: #dc3545;
        }

        .payments-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .payments-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-table th,
        .payments-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .payments-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .payment-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .payment-status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .payment-status.completed {
            background: #d4edda;
            color: #155724;
        }

        .payment-status.failed {
            background: #f8d7da;
            color: #721c24;
        }

        .payment-status.cancelled {
            background: #e2e3e5;
            color: #383d41;
        }

        .payment-method {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
        }

        .payment-method.wave {
            color: #25D366;
        }

        .payment-method.orange_money {
            color: #FF6600;
        }

        .payment-amount {
            font-weight: 700;
            color: #333;
        }

        .payment-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-view {
            background: #007bff;
            color: white;
        }

        .btn-view:hover {
            background: #0056b3;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .filter-group select,
        .filter-group input {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }

        .btn-filter {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: #0056b3;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .pagination button:hover {
            background: #f8f9fa;
        }

        .pagination button.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .payments-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .payments-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .payments-table {
                overflow-x: auto;
            }

            .payments-table table {
                min-width: 600px;
            }
        }
    </style>
</head>
<body>
    <div class="payments-container">
        <div class="payments-header">
            <h1 class="payments-title">
                <i class="fas fa-credit-card"></i>
                Gestion des Paiements
            </h1>
            <button class="btn-filter" onclick="refreshPayments()">
                <i class="fas fa-sync-alt"></i>
                Actualiser
            </button>
        </div>

        <!-- Statistiques -->
        <div class="payments-stats" id="paymentsStats">
            <div class="stat-card revenue">
                <div class="stat-value" id="totalRevenue">0 XOF</div>
                <div class="stat-label">Revenus Totaux</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-value" id="completedPayments">0</div>
                <div class="stat-label">Paiements Réussis</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-value" id="pendingPayments">0</div>
                <div class="stat-label">Paiements en Attente</div>
            </div>
            <div class="stat-card failed">
                <div class="stat-value" id="failedPayments">0</div>
                <div class="stat-label">Paiements Échoués</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filter-section">
            <form class="filter-form" id="filterForm">
                <div class="filter-group">
                    <label for="statusFilter">Statut</label>
                    <select id="statusFilter" name="status">
                        <option value="">Tous</option>
                        <option value="pending">En attente</option>
                        <option value="completed">Réussi</option>
                        <option value="failed">Échoué</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="methodFilter">Méthode</label>
                    <select id="methodFilter" name="method">
                        <option value="">Toutes</option>
                        <option value="wave">Wave</option>
                        <option value="orange_money">Orange Money</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="dateFilter">Date</label>
                    <input type="date" id="dateFilter" name="date">
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i>
                    Filtrer
                </button>
            </form>
        </div>

        <!-- Tableau des paiements -->
        <div class="payments-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Étudiant</th>
                        <th>Montant</th>
                        <th>Méthode</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Transaction</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentsTableBody">
                    <!-- Les paiements seront chargés ici -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination" id="pagination">
            <!-- La pagination sera générée ici -->
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentFilters = {};

        // Charger les statistiques
        async function loadStats() {
            try {
                const response = await fetch('api/payment.php?action=stats');
                const result = await response.json();
                
                if (result.success) {
                    const stats = result.data;
                    document.getElementById('totalRevenue').textContent = `${stats.total_revenue || 0} XOF`;
                    document.getElementById('completedPayments').textContent = stats.completed_payments || 0;
                    document.getElementById('pendingPayments').textContent = stats.pending_payments || 0;
                    document.getElementById('failedPayments').textContent = stats.failed_payments || 0;
                }
            } catch (error) {
                console.error('Erreur chargement statistiques:', error);
            }
        }

        // Charger les paiements
        async function loadPayments(page = 1, filters = {}) {
            try {
                const params = new URLSearchParams({
                    action: 'history',
                    limit: 20,
                    offset: (page - 1) * 20,
                    ...filters
                });

                const response = await fetch(`api/payment.php?${params}`);
                const result = await response.json();
                
                if (result.success) {
                    displayPayments(result.data);
                }
            } catch (error) {
                console.error('Erreur chargement paiements:', error);
            }
        }

        // Afficher les paiements
        function displayPayments(payments) {
            const tbody = document.getElementById('paymentsTableBody');
            tbody.innerHTML = '';

            payments.forEach(payment => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>#${payment.id}</td>
                    <td>${payment.student_name || '-'}</td>
                    <td class="payment-amount">${payment.amount} XOF</td>
                    <td>
                        <span class="payment-method ${payment.method}">
                            ${getMethodIcon(payment.method)} ${getMethodName(payment.method)}
                        </span>
                    </td>
                    <td>
                        <span class="payment-status ${payment.status}">
                            ${getStatusIcon(payment.status)} ${getStatusLabel(payment.status)}
                        </span>
                    </td>
                    <td>${formatDate(payment.created_at)}</td>
                    <td>${payment.transaction_id || '-'}</td>
                    <td>
                        <div class="payment-actions">
                            <button class="btn-action btn-view" onclick="viewPaymentDetails('${payment.session_id}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${payment.status === 'pending' ? `
                                <button class="btn-action btn-cancel" onclick="cancelPayment('${payment.session_id}')">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Obtenir l'icône de la méthode
        function getMethodIcon(method) {
            const icons = {
                'wave': '<i class="fab fa-whatsapp"></i>',
                'orange_money': '<i class="fas fa-mobile-alt"></i>',
                'credit_card': '<i class="fas fa-credit-card"></i>'
            };
            return icons[method] || '';
        }

        // Obtenir le nom de la méthode
        function getMethodName(method) {
            const names = {
                'wave': 'Wave',
                'orange_money': 'Orange Money',
                'credit_card': 'Carte de crédit'
            };
            return names[method] || method;
        }

        // Obtenir l'icône du statut
        function getStatusIcon(status) {
            const icons = {
                'pending': '<i class="fas fa-clock"></i>',
                'completed': '<i class="fas fa-check"></i>',
                'failed': '<i class="fas fa-times"></i>',
                'cancelled': '<i class="fas fa-ban"></i>'
            };
            return icons[status] || '';
        }

        // Obtenir le libellé du statut
        function getStatusLabel(status) {
            const labels = {
                'pending': 'En attente',
                'completed': 'Réussi',
                'failed': 'Échoué',
                'cancelled': 'Annulé'
            };
            return labels[status] || status;
        }

        // Formater la date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Voir les détails d'un paiement
        function viewPaymentDetails(sessionId) {
            // Ouvrir une modal avec les détails
            window.open(`payment-details.php?session_id=${sessionId}`, '_blank');
        }

        // Annuler un paiement
        async function cancelPayment(sessionId) {
            if (!confirm('Êtes-vous sûr de vouloir annuler ce paiement ?')) {
                return;
            }

            try {
                const response = await fetch('api/payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'cancel',
                        session_id: sessionId
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Paiement annulé avec succès');
                    loadPayments(currentPage, currentFilters);
                    loadStats();
                } else {
                    alert('Erreur: ' + result.error);
                }
            } catch (error) {
                console.error('Erreur annulation paiement:', error);
                alert('Erreur lors de l\'annulation du paiement');
            }
        }

        // Rafraîchir les paiements
        function refreshPayments() {
            loadPayments(currentPage, currentFilters);
            loadStats();
        }

        // Gestion du formulaire de filtre
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            currentFilters = {};
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    currentFilters[key] = value;
                }
            }
            
            currentPage = 1;
            loadPayments(currentPage, currentFilters);
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadPayments();
            
            // Rafraîchir automatiquement toutes les 30 secondes
            setInterval(() => {
                loadStats();
                loadPayments(currentPage, currentFilters);
            }, 30000);
        });
    </script>
</body>
</html>

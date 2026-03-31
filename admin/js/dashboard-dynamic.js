// Dashboard dynamique - Linekode Admin
class DashboardManager {
    constructor() {
        this.init();
    }

    init() {
        this.loadDashboardData();
        this.setupAutoRefresh();
        this.setupAnimations();
    }

    loadDashboardData() {
        const stats = window.adminSystem.getStats();
        const inscriptions = window.adminSystem.getInscriptions();
        const messages = window.adminSystem.getMessages(true);
        const annonces = window.adminSystem.getAnnonces();

        // Mettre à jour les cartes statistiques
        this.updateStatCard('totalInscriptions', stats.totalInscriptions);
        this.updateStatCard('pendingInscriptions', inscriptions.filter(i => i.status === 'new').length);
        this.updateStatCard('totalAnnonces', annonces.length);
        this.updateStatCard('unreadMessages', messages.length);

        // Mettre à jour le tableau des inscriptions récentes
        this.updateRecentInscriptions(inscriptions.slice(0, 5));
    }

    updateStatCard(id, value) {
        const element = document.querySelector(`.stat-card:nth-child(${this.getStatCardIndex(id)}) .stat-number`);
        if (element) {
            this.animateNumber(element, parseInt(element.textContent) || 0, value);
        }
    }

    getStatCardIndex(id) {
        const mapping = {
            'totalInscriptions': 1,
            'pendingInscriptions': 2,
            'totalAnnonces': 3,
            'unreadMessages': 4
        };
        return mapping[id] || 1;
    }

    animateNumber(element, start, end) {
        const duration = 1000;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const current = Math.floor(start + (end - start) * progress);
            
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    updateRecentInscriptions(inscriptions) {
        const tbody = document.querySelector('.table tbody');
        if (!tbody) return;

        tbody.innerHTML = inscriptions.map(inscription => `
            <tr>
                <td>${inscription.name}</td>
                <td>${inscription.formation}</td>
                <td>${inscription.date}</td>
                <td>${this.getStatusBadge(inscription.status)}</td>
                <td>
                    <button class="btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="viewInscription(${inscription.id})">
                        <i class="fas fa-eye"></i> Voir
                    </button>
                </td>
            </tr>
        `).join('');
    }

    getStatusBadge(status) {
        const badges = {
            'new': '<span class="badge badge-new">Nouveau</span>',
            'pending': '<span class="badge badge-pending">En attente</span>',
            'confirmed': '<span class="badge badge-confirmed">Confirmé</span>',
            'cancelled': '<span class="badge badge-cancelled">Annulé</span>'
        };
        return badges[status] || '<span class="badge badge-new">Inconnu</span>';
    }

    setupAutoRefresh() {
        // Rafraîchir les données toutes les 30 secondes
        setInterval(() => {
            this.loadDashboardData();
            this.showNotification('Données actualisées', 'success');
        }, 30000);
    }

    setupAnimations() {
        // Animation des cartes au chargement
        const cards = document.querySelectorAll('.stat-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : '#0284c7'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}

// Fonctions globales pour les interactions
function viewInscription(id) {
    window.location.href = `inscriptions.html?id=${id}`;
}

function exportData() {
    window.adminSystem.exportData();
}

// Initialiser le dashboard au chargement
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('dashboard.html')) {
        window.dashboardManager = new DashboardManager();
    }
});

// Gestion dynamique des inscriptions - Linekode Admin
class InscriptionsManager {
    constructor() {
        this.currentFilter = {};
        this.init();
    }

    init() {
        this.loadInscriptions();
        this.setupFilters();
        this.setupSearch();
        this.setupModal();
    }

    loadInscriptions() {
        const inscriptions = window.adminSystem.getInscriptions(this.currentFilter);
        this.renderInscriptions(inscriptions);
        this.updateStats();
    }

    renderInscriptions(inscriptions) {
        const tbody = document.querySelector('#inscriptionsTable tbody');
        if (!tbody) return;

        if (inscriptions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-user-slash" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                        Aucune inscription trouvée
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = inscriptions.map(inscription => `
            <tr data-id="${inscription.id}">
                <td>${inscription.name}</td>
                <td>${inscription.email}</td>
                <td>${inscription.phone}</td>
                <td>${inscription.formation}</td>
                <td>${inscription.date}</td>
                <td>${this.getStatusBadge(inscription.status)}</td>
                <td>
                    <div class="actions">
                        <button class="btn-action btn-view" onclick="inscriptionsManager.viewStudent(${inscription.id})" title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${inscription.status !== 'confirmed' ? `
                            <button class="btn-action btn-confirm" onclick="inscriptionsManager.confirmInscription(${inscription.id})" title="Confirmer">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                        <button class="btn-action btn-delete" onclick="inscriptionsManager.deleteInscription(${inscription.id})" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
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

    setupFilters() {
        const statusFilter = document.getElementById('statusFilter');
        const formationFilter = document.getElementById('formationFilter');

        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.currentFilter.status = e.target.value || null;
                this.loadInscriptions();
            });
        }

        if (formationFilter) {
            formationFilter.addEventListener('change', (e) => {
                this.currentFilter.formation = e.target.value || null;
                this.loadInscriptions();
            });
        }
    }

    setupSearch() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.currentFilter.search = e.target.value || null;
                    this.loadInscriptions();
                }, 300);
            });
        }
    }

    setupModal() {
        const modal = document.getElementById('studentModal');
        const closeBtn = modal?.querySelector('.modal-close');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeModal());
        }

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeModal();
            }
        });
    }

    viewStudent(id) {
        const inscription = window.adminSystem.getInscriptions().find(i => i.id === id);
        if (!inscription) return;

        const detailsHtml = `
            <div class="info-row">
                <span class="info-label">Nom complet:</span>
                <span class="info-value">${inscription.name}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">${inscription.email}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Téléphone:</span>
                <span class="info-value">${inscription.phone}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Formation:</span>
                <span class="info-value">${inscription.formation}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date d'inscription:</span>
                <span class="info-value">${inscription.date}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Statut:</span>
                <span class="info-value">${this.getStatusText(inscription.status)}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Adresse:</span>
                <span class="info-value">${inscription.address || 'Non spécifiée'}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Motivation:</span>
                <span class="info-value">${inscription.motivation || 'Non spécifiée'}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Actions:</span>
                <span class="info-value">
                    ${inscription.status !== 'confirmed' ? `
                        <button class="btn-action btn-confirm" onclick="inscriptionsManager.confirmInscription(${inscription.id})" style="margin-right: 10px;">
                            <i class="fas fa-check"></i> Confirmer
                        </button>
                    ` : ''}
                    <button class="btn-action btn-delete" onclick="inscriptionsManager.deleteInscription(${inscription.id})">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </span>
            </div>
        `;

        document.getElementById('studentDetails').innerHTML = detailsHtml;
        document.getElementById('studentModal').style.display = 'block';
    }

    getStatusText(status) {
        const texts = {
            'new': 'Nouveau',
            'pending': 'En attente',
            'confirmed': 'Confirmé',
            'cancelled': 'Annulé'
        };
        return texts[status] || 'Inconnu';
    }

    closeModal() {
        document.getElementById('studentModal').style.display = 'none';
    }

    confirmInscription(id) {
        const updated = window.adminSystem.updateInscription(id, { status: 'confirmed' });
        if (updated) {
            this.showNotification(`Inscription de ${updated.name} confirmée!`, 'success');
            this.loadInscriptions();
            this.closeModal();
        }
    }

    deleteInscription(id) {
        const inscription = window.adminSystem.getInscriptions().find(i => i.id === id);
        if (!inscription) return;

        if (confirm(`Supprimer l'inscription de ${inscription.name}?`)) {
            const deleted = window.adminSystem.deleteInscription(id);
            if (deleted) {
                this.showNotification(`Inscription de ${inscription.name} supprimée!`, 'success');
                this.loadInscriptions();
                this.closeModal();
            }
        }
    }

    updateStats() {
        const inscriptions = window.adminSystem.getInscriptions();
        const stats = {
            total: inscriptions.length,
            new: inscriptions.filter(i => i.status === 'new').length,
            pending: inscriptions.filter(i => i.status === 'pending').length,
            confirmed: inscriptions.filter(i => i.status === 'confirmed').length
        };

        // Mettre à jour les statistiques si elles existent sur la page
        const statElements = {
            total: document.querySelector('.stat-total'),
            new: document.querySelector('.stat-new'),
            pending: document.querySelector('.stat-pending'),
            confirmed: document.querySelector('.stat-confirmed')
        };

        Object.keys(stats).forEach(key => {
            if (statElements[key]) {
                statElements[key].textContent = stats[key];
            }
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
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    exportInscriptions() {
        const inscriptions = window.adminSystem.getInscriptions();
        const csv = this.convertToCSV(inscriptions);
        this.downloadCSV(csv, 'inscriptions.csv');
    }

    convertToCSV(data) {
        const headers = ['Nom', 'Email', 'Téléphone', 'Formation', 'Date', 'Statut', 'Adresse', 'Motivation'];
        const csvContent = [
            headers.join(','),
            ...data.map(item => [
                `"${item.name}"`,
                `"${item.email}"`,
                `"${item.phone}"`,
                `"${item.formation}"`,
                `"${item.date}"`,
                `"${this.getStatusText(item.status)}"`,
                `"${item.address || ''}"`,
                `"${item.motivation || ''}"`
            ].join(','))
        ].join('\n');
        
        return csvContent;
    }

    downloadCSV(csv, filename) {
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Initialiser le gestionnaire d'inscriptions
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('inscriptions.html')) {
        window.inscriptionsManager = new InscriptionsManager();
    }
});

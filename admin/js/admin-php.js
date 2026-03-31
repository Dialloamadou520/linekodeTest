// JavaScript pour l'administration PHP - Linekode
// Communication avec l'API PHP

class AdminPHP {
    constructor() {
        this.baseURL = 'api/';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupAutoRefresh();
        this.setupNotifications();
    }

    setupEventListeners() {
        // Écouter les clics sur les boutons d'action
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-view')) {
                const id = e.target.dataset.id;
                this.viewItem('inscriptions', id);
            }
            
            if (e.target.classList.contains('btn-confirm')) {
                const id = e.target.dataset.id;
                this.confirmInscription(id);
            }
            
            if (e.target.classList.contains('btn-delete')) {
                const id = e.target.dataset.id;
                this.deleteItem('inscriptions', id);
            }
            
            if (e.target.classList.contains('btn-publish')) {
                const id = e.target.dataset.id;
                this.publishAnnonce(id);
            }
            
            if (e.target.classList.contains('btn-reply')) {
                const id = e.target.dataset.id;
                this.replyMessage(id);
            }
        });

        // Écouter les soumissions de formulaires
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'inscriptionForm') {
                e.preventDefault();
                this.handleInscriptionForm(e.target);
            }
            
            if (e.target.id === 'annonceForm') {
                e.preventDefault();
                this.handleAnnonceForm(e.target);
            }
            
            if (e.target.id === 'replyForm') {
                e.preventDefault();
                this.handleReplyForm(e.target);
            }
        });
    }

    setupAutoRefresh() {
        // Rafraîchir les données toutes les 30 secondes
        setInterval(() => {
            this.refreshCurrentPage();
        }, 30000);
    }

    setupNotifications() {
        // Créer le conteneur de notifications
        if (!document.getElementById('notificationContainer')) {
            const container = document.createElement('div');
            container.id = 'notificationContainer';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
            `;
            document.body.appendChild(container);
        }
    }

    // API Calls
    async apiCall(endpoint, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(this.baseURL + endpoint, options);
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.error || 'Erreur HTTP');
            }
            
            return result;
        } catch (error) {
            console.error('API Error:', error);
            this.showNotification('Erreur: ' + error.message, 'error');
            throw error;
        }
    }

    // Dashboard
    async loadDashboardStats() {
        try {
            const response = await this.apiCall('dashboard.php?action=stats');
            if (response.success) {
                this.updateStatCards(response.data);
            }
        } catch (error) {
            console.error('Erreur de chargement du dashboard:', error);
        }
    }

    updateStatCards(stats) {
        const cards = [
            { id: 'totalInscriptions', value: stats.totalInscriptions },
            { id: 'newInscriptions', value: stats.newInscriptions },
            { id: 'totalAnnonces', value: stats.totalAnnonces },
            { id: 'unreadMessages', value: stats.unreadMessages }
        ];

        cards.forEach(card => {
            const element = document.querySelector(`[data-stat="${card.id}"]`);
            if (element) {
                this.animateNumber(element, parseInt(element.textContent) || 0, card.value);
            }
        });
    }

    animateNumber(element, start, end) {
        const duration = 1000;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const current = Math.floor(start + (end - start) * progress);
            
            element.textContent = current.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    // Inscriptions
    async loadInscriptions() {
        try {
            const response = await this.apiCall('inscriptions.php?action=list');
            if (response.success) {
                this.renderInscriptions(response.data);
            }
        } catch (error) {
            console.error('Erreur de chargement des inscriptions:', error);
        }
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
                        <button class="btn-action btn-view" data-id="${inscription.id}" title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${inscription.status !== 'confirmed' ? `
                            <button class="btn-action btn-confirm" data-id="${inscription.id}" title="Confirmer">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                        <button class="btn-action btn-delete" data-id="${inscription.id}" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    async confirmInscription(id) {
        if (!confirm('Confirmer cette inscription?')) return;

        try {
            const response = await this.apiCall('inscriptions.php?action=update&id=' + id, 'PUT', {
                status: 'confirmed'
            });
            
            if (response.success) {
                this.showNotification('Inscription confirmée avec succès!', 'success');
                this.loadInscriptions();
            }
        } catch (error) {
            console.error('Erreur lors de la confirmation:', error);
        }
    }

    async deleteInscription(id) {
        const inscription = await this.getInscription(id);
        if (!inscription) return;

        if (!confirm(`Supprimer l'inscription de ${inscription.name}?`)) return;

        try {
            const response = await this.apiCall('inscriptions.php?action=delete&id=' + id, 'DELETE');
            
            if (response.success) {
                this.showNotification(`Inscription de ${inscription.name} supprimée!`, 'success');
                this.loadInscriptions();
            }
        } catch (error) {
            console.error('Erreur lors de la suppression:', error);
        }
    }

    async getInscription(id) {
        try {
            const response = await this.apiCall('inscriptions.php?action=list');
            if (response.success) {
                return response.data.find(i => i.id == id);
            }
        } catch (error) {
            console.error('Erreur:', error);
            return null;
        }
    }

    // Messages
    async loadMessages() {
        try {
            const response = await this.apiCall('messages.php?action=list');
            if (response.success) {
                this.renderMessages(response.data);
            }
        } catch (error) {
            console.error('Erreur de chargement des messages:', error);
        }
    }

    renderMessages(messages) {
        const list = document.getElementById('messagesList');
        if (!list) return;

        if (messages.length === 0) {
            list.innerHTML = `
                <div style="text-align: center; padding: 60px; color: #6b7280;">
                    <i class="fas fa-envelope" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                    <h3>Aucun message</h3>
                    <p>Vous n'avez reçu aucun message pour le moment.</p>
                </div>
            `;
            return;
        }

        list.innerHTML = messages.map(message => `
            <div class="message-item ${!message.read_status ? 'unread' : ''}" data-id="${message.id}" onclick="adminPHP.viewMessage(${message.id})">
                <div class="message-header">
                    <div>
                        <div class="message-sender">${message.sender}</div>
                        <div class="message-meta">
                            <span><i class="fas fa-envelope"></i> ${message.email}</span>
                            <span><i class="fas fa-phone"></i> ${message.phone}</span>
                            <span><i class="fas fa-calendar"></i> ${message.date}</span>
                        </div>
                    </div>
                    <span class="badge badge-${message.read_status ? 'read' : 'unread'}">${message.read_status ? 'Lu' : 'Non lu'}</span>
                </div>
                <div class="message-subject">${message.subject}</div>
                <div class="message-preview">
                    ${this.truncateText(message.content, 120)}
                </div>
                <div class="message-actions">
                    <button class="btn-action btn-view" onclick="event.stopPropagation(); adminPHP.viewMessage(${message.id})" title="Voir">
                        <i class="fas fa-eye"></i> Voir
                    </button>
                    <button class="btn-action btn-reply" onclick="event.stopPropagation(); adminPHP.replyMessage(${message.id})" title="Répondre">
                        <i class="fas fa-reply"></i> Répondre
                    </button>
                    <button class="btn-action btn-delete" onclick="event.stopPropagation(); adminPHP.deleteMessage(${message.id})" title="Supprimer">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Annonces
    async loadAnnonces() {
        try {
            const response = await this.apiCall('annonces.php?action=list');
            if (response.success) {
                this.renderAnnonces(response.data);
            }
        } catch (error) {
            console.error('Erreur de chargement des annonces:', error);
        }
    }

    renderAnnonces(annonces) {
        const grid = document.getElementById('annoncesGrid');
        if (!grid) return;

        if (annonces.length === 0) {
            grid.innerHTML = `
                <div style="text-align: center; padding: 60px; color: #6b7280;">
                    <i class="fas fa-bullhorn" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                    <h3>Aucune annonce</h3>
                    <p>Créez votre première annonce pour commencer.</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = annonces.map(annonce => `
            <div class="annonce-card" data-id="${annonce.id}">
                <div class="annonce-header">
                    <div>
                        <div class="annonce-title">${annonce.title}</div>
                        <div class="annonce-meta">
                            <span><i class="fas fa-calendar"></i> ${annonce.date}</span>
                            <span><i class="fas fa-user"></i> ${annonce.author}</span>
                        </div>
                    </div>
                    <span class="badge badge-${annonce.status}">${this.getStatusText(annonce.status)}</span>
                </div>
                <div class="annonce-content">
                    ${this.truncateText(annonce.content, 150)}
                </div>
                <div class="annonce-actions">
                    <button class="btn-action btn-edit" onclick="adminPHP.editAnnonce(${annonce.id})" title="Modifier">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                    ${annonce.status !== 'published' ? `
                        <button class="btn-action btn-publish" onclick="adminPHP.publishAnnonce(${annonce.id})" title="Publier">
                            <i class="fas fa-paper-plane"></i> Publier
                        </button>
                    ` : ''}
                    <button class="btn-action btn-delete" onclick="adminPHP.deleteAnnonce(${annonce.id})" title="Supprimer">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Utilitaires
    getStatusBadge(status) {
        const badges = {
            'new': '<span class="badge badge-new">Nouveau</span>',
            'pending': '<span class="badge badge-pending">En attente</span>',
            'confirmed': '<span class="badge badge-confirmed">Confirmé</span>',
            'cancelled': '<span class="badge badge-cancelled">Annulé</span>'
        };
        return badges[status] || '<span class="badge badge-new">Inconnu</span>';
    }

    getStatusText(status) {
        const texts = {
            'draft': 'Brouillon',
            'published': 'Publié',
            'scheduled': 'Programmé'
        };
        return texts[status] || 'Brouillon';
    }

    truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    showNotification(message, type = 'info', duration = 3000) {
        const container = document.getElementById('notificationContainer');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        notification.style.cssText = `
            background: ${type === 'success' ? '#10b981' : '#0284c7'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            margin-bottom: 10px;
            animation: slideIn 0.3s ease;
        `;
        
        container.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, duration);
    }

    refreshCurrentPage() {
        const currentPath = window.location.pathname;
        
        if (currentPath.includes('dashboard.php')) {
            this.loadDashboardStats();
        } else if (currentPath.includes('inscriptions.php')) {
            this.loadInscriptions();
        } else if (currentPath.includes('messages.php')) {
            this.loadMessages();
        } else if (currentPath.includes('annonces.php')) {
            this.loadAnnonces();
        }
    }

    // Rendre les fonctions globales
    window.adminPHP = {
        viewItem: (type, id) => this.viewItem(type, id),
        confirmInscription: (id) => this.confirmInscription(id),
        deleteItem: (type, id) => this.deleteItem(type, id),
        publishAnnonce: (id) => this.publishAnnonce(id),
        editAnnonce: (id) => this.editAnnonce(id),
        deleteAnnonce: (id) => this.deleteAnnonce(id),
        viewMessage: (id) => this.viewMessage(id),
        replyMessage: (id) => this.replyMessage(id),
        deleteMessage: (id) => this.deleteMessage(id)
    };
}

// Initialiser l'administration PHP
document.addEventListener('DOMContentLoaded', function() {
    window.adminPHP = new AdminPHP();
});

// Exporter pour utilisation globale
window.AdminPHP = AdminPHP;

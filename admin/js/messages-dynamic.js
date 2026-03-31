// Gestion dynamique des messages - Linekode Admin
class MessagesManager {
    constructor() {
        this.init();
    }

    init() {
        this.loadMessages();
        this.setupModal();
        this.setupReplyForm();
        this.setupFilters();
    }

    loadMessages() {
        const messages = window.adminSystem.getMessages();
        this.renderMessages(messages);
        this.updateUnreadCount();
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
            <div class="message-item ${!message.read ? 'unread' : ''}" data-id="${message.id}" onclick="messagesManager.viewMessage(${message.id})">
                <div class="message-header">
                    <div>
                        <div class="message-sender">${message.sender}</div>
                        <div class="message-meta">
                            <span><i class="fas fa-envelope"></i> ${message.email}</span>
                            <span><i class="fas fa-phone"></i> ${message.phone}</span>
                            <span><i class="fas fa-calendar"></i> ${message.date}</span>
                        </div>
                    </div>
                    <span class="badge badge-${message.read ? 'read' : 'unread'}">${message.read ? 'Lu' : 'Non lu'}</span>
                </div>
                <div class="message-subject">${message.subject}</div>
                <div class="message-preview">
                    ${this.truncateText(message.content, 120)}
                </div>
                <div class="message-actions">
                    <button class="btn-action btn-view" onclick="event.stopPropagation(); messagesManager.viewMessage(${message.id})" title="Voir">
                        <i class="fas fa-eye"></i> Voir
                    </button>
                    <button class="btn-action btn-reply" onclick="event.stopPropagation(); messagesManager.replyMessage(${message.id})" title="Répondre">
                        <i class="fas fa-reply"></i> Répondre
                    </button>
                    <button class="btn-action btn-delete" onclick="event.stopPropagation(); messagesManager.deleteMessage(${message.id})" title="Supprimer">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        `).join('');
    }

    truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    setupModal() {
        const modal = document.getElementById('messageModal');
        const closeBtn = modal?.querySelector('.modal-close');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeMessageModal());
        }

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeMessageModal();
            }
        });
    }

    setupReplyForm() {
        const form = document.querySelector('.reply-form');
        if (form) {
            form.addEventListener('submit', (e) => this.sendReply(e));
        }
    }

    setupFilters() {
        // Ajouter des filtres si nécessaire
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const filter = e.target.dataset.filter;
                this.filterMessages(filter);
            });
        });
    }

    viewMessage(id) {
        const message = window.adminSystem.getMessages().find(m => m.id === id);
        if (!message) return;

        // Marquer comme lu
        if (!message.read) {
            window.adminSystem.markMessageAsRead(id);
            message.read = true;
            this.updateUnreadCount();
        }

        const detailHtml = `
            <div class="message-detail">
                <div class="message-detail-header">
                    <div class="message-detail-sender">${message.sender}</div>
                    <div class="message-detail-subject">${message.subject}</div>
                    <div class="message-detail-meta">
                        <span><i class="fas fa-envelope"></i> ${message.email}</span>
                        <span><i class="fas fa-phone"></i> ${message.phone}</span>
                        <span><i class="fas fa-calendar"></i> ${message.date}</span>
                    </div>
                </div>
                <div class="message-detail-content">
                    ${message.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="reply-section">
                <h3>Répondre à ce message</h3>
                <form class="reply-form" onsubmit="messagesManager.sendReply(event, ${id})">
                    <div class="form-group">
                        <label for="replyContent">Votre réponse</label>
                        <textarea id="replyContent" name="replyContent" required placeholder="Écrivez votre réponse ici..."></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="messagesManager.closeMessageModal()">Annuler</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Envoyer la réponse
                        </button>
                    </div>
                </form>
            </div>
        `;

        document.getElementById('messageDetail').innerHTML = detailHtml;
        document.getElementById('messageModal').style.display = 'block';

        // Mettre à jour le statut dans la liste
        const messageItem = document.querySelector(`.message-item[data-id="${id}"]`);
        if (messageItem) {
            messageItem.classList.remove('unread');
            const badge = messageItem.querySelector('.badge');
            if (badge) {
                badge.classList.remove('badge-unread');
                badge.classList.add('badge-read');
                badge.textContent = 'Lu';
            }
        }
    }

    closeMessageModal() {
        document.getElementById('messageModal').style.display = 'none';
    }

    replyMessage(id) {
        this.viewMessage(id);
        setTimeout(() => {
            const replyTextarea = document.getElementById('replyContent');
            if (replyTextarea) {
                replyTextarea.focus();
            }
        }, 100);
    }

    sendReply(event, messageId) {
        event.preventDefault();
        
        const replyContent = document.getElementById('replyContent').value;
        const message = window.adminSystem.getMessages().find(m => m.id === messageId);
        
        if (!message) return;

        // Simuler l'envoi d'email
        this.sendEmail(message.email, `Re: ${message.subject}`, replyContent);
        
        this.showNotification(`Réponse envoyée à ${message.sender}!`, 'success');
        this.closeMessageModal();
    }

    sendEmail(to, subject, content) {
        // Simuler l'envoi d'email (dans un vrai système, vous utiliseriez une API)
        console.log('Email envoyé:', {
            to: to,
            subject: subject,
            content: content,
            date: new Date().toISOString()
        });

        // Sauvegarder la réponse dans les données
        const replyData = {
            to: to,
            subject: subject,
            content: content,
            date: new Date().toISOString(),
            type: 'sent'
        };

        // Ajouter aux messages envoyés
        const data = window.adminSystem.getData();
        if (!data.sentMessages) data.sentMessages = [];
        data.sentMessages.push(replyData);
        window.adminSystem.saveData(data);
    }

    deleteMessage(id) {
        const message = window.adminSystem.getMessages().find(m => m.id === id);
        if (!message) return;

        if (confirm(`Supprimer le message de ${message.sender}?`)) {
            const deleted = window.adminSystem.deleteMessage(id);
            if (deleted) {
                this.showNotification(`Message de ${message.sender} supprimé avec succès!`, 'success');
                this.loadMessages();
                this.closeMessageModal();
            }
        }
    }

    updateUnreadCount() {
        const unreadMessages = window.adminSystem.getMessages(true);
        const count = unreadMessages.length;
        
        // Mettre à jour le badge dans le menu
        const badge = document.querySelector('.menu-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }

        // Mettre à jour le titre de la page
        const title = document.querySelector('.page-title');
        if (title) {
            title.textContent = `Messages des visiteurs${count > 0 ? ` (${count})` : ''}`;
        }
    }

    filterMessages(filter) {
        let messages = window.adminSystem.getMessages();
        
        switch(filter) {
            case 'unread':
                messages = messages.filter(m => !m.read);
                break;
            case 'today':
                const today = new Date().toDateString();
                messages = messages.filter(m => new Date(m.date).toDateString() === today);
                break;
            case 'week':
                const weekAgo = new Date();
                weekAgo.setDate(weekAgo.getDate() - 7);
                messages = messages.filter(m => new Date(m.date) >= weekAgo);
                break;
        }
        
        this.renderMessages(messages);
    }

    searchMessages(query) {
        const messages = window.adminSystem.getMessages();
        const filtered = messages.filter(message => 
            message.sender.toLowerCase().includes(query.toLowerCase()) ||
            message.subject.toLowerCase().includes(query.toLowerCase()) ||
            message.content.toLowerCase().includes(query.toLowerCase()) ||
            message.email.toLowerCase().includes(query.toLowerCase())
        );
        this.renderMessages(filtered);
    }

    markAllAsRead() {
        const messages = window.adminSystem.getMessages(true);
        messages.forEach(message => {
            window.adminSystem.markMessageAsRead(message.id);
        });
        this.loadMessages();
        this.showNotification('Tous les messages marqués comme lus', 'success');
    }

    exportMessages() {
        const messages = window.adminSystem.getMessages();
        const csv = this.convertToCSV(messages);
        this.downloadCSV(csv, 'messages.csv');
    }

    convertToCSV(data) {
        const headers = ['Expéditeur', 'Email', 'Téléphone', 'Sujet', 'Date', 'Lu', 'Contenu'];
        const csvContent = [
            headers.join(','),
            ...data.map(item => [
                `"${item.sender}"`,
                `"${item.email}"`,
                `"${item.phone}"`,
                `"${item.subject}"`,
                `"${item.date}"`,
                `"${item.read ? 'Oui' : 'Non'}"`,
                `"${item.content.replace(/"/g, '""')}"`
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
}

// Initialiser le gestionnaire de messages
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('messages.html')) {
        window.messagesManager = new MessagesManager();
    }
});

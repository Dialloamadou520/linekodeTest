// Gestion dynamique des annonces - Linekode Admin
class AnnoncesManager {
    constructor() {
        this.currentEditId = null;
        this.init();
    }

    init() {
        this.loadAnnonces();
        this.setupModal();
        this.setupForm();
    }

    loadAnnonces() {
        const annonces = window.adminSystem.getAnnonces();
        this.renderAnnonces(annonces);
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
                    <button class="btn-action btn-edit" onclick="annoncesManager.editAnnonce(${annonce.id})" title="Modifier">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                    ${annonce.status !== 'published' ? `
                        <button class="btn-action btn-publish" onclick="annoncesManager.publishAnnonce(${annonce.id})" title="Publier">
                            <i class="fas fa-paper-plane"></i> Publier
                        </button>
                    ` : ''}
                    <button class="btn-action btn-delete" onclick="annoncesManager.deleteAnnonce(${annonce.id})" title="Supprimer">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        `).join('');
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

    setupModal() {
        const modal = document.getElementById('annonceModal');
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

    setupForm() {
        const form = document.getElementById('annonceForm');
        if (form) {
            form.addEventListener('submit', (e) => this.saveAnnonce(e));
        }
    }

    openAnnonceModal() {
        this.currentEditId = null;
        document.getElementById('modalTitle').textContent = 'Nouvelle annonce';
        document.getElementById('annonceForm').reset();
        document.getElementById('annonceDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('annonceModal').style.display = 'block';
    }

    closeModal() {
        document.getElementById('annonceModal').style.display = 'none';
        this.currentEditId = null;
    }

    editAnnonce(id) {
        this.currentEditId = id;
        const annonce = window.adminSystem.getAnnonces().find(a => a.id === id);
        if (!annonce) return;

        document.getElementById('modalTitle').textContent = 'Modifier l\'annonce';
        document.getElementById('annonceTitle').value = annonce.title;
        document.getElementById('annonceContent').value = annonce.content;
        document.getElementById('annonceStatus').value = annonce.status;
        document.getElementById('annonceDate').value = annonce.date;
        
        document.getElementById('annonceModal').style.display = 'block';
    }

    saveAnnonce(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const annonce = {
            title: formData.get('title'),
            content: formData.get('content'),
            status: formData.get('status'),
            date: formData.get('date')
        };

        if (this.currentEditId) {
            const updated = window.adminSystem.updateAnnonce(this.currentEditId, annonce);
            if (updated) {
                this.showNotification(`Annonce "${updated.title}" modifiée avec succès!`, 'success');
            }
        } else {
            const created = window.adminSystem.addAnnonce(annonce);
            if (created) {
                this.showNotification(`Annonce "${created.title}" créée avec succès!`, 'success');
            }
        }

        this.closeModal();
        this.loadAnnonces();
    }

    publishAnnonce(id) {
        const updated = window.adminSystem.updateAnnonce(id, { status: 'published' });
        if (updated) {
            this.showNotification(`Annonce "${updated.title}" publiée avec succès!`, 'success');
            this.loadAnnonces();
        }
    }

    deleteAnnonce(id) {
        const annonce = window.adminSystem.getAnnonces().find(a => a.id === id);
        if (!annonce) return;

        if (confirm(`Supprimer l'annonce "${annonce.title}"?`)) {
            const deleted = window.adminSystem.deleteAnnonce(id);
            if (deleted) {
                this.showNotification(`Annonce "${annonce.title}" supprimée avec succès!`, 'success');
                this.loadAnnonces();
            }
        }
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

    // Fonctionnalités avancées
    scheduleAnnonce(id, publishDate) {
        const updated = window.adminSystem.updateAnnonce(id, { 
            status: 'scheduled',
            publishDate: publishDate 
        });
        if (updated) {
            this.showNotification(`Annonce programmée pour le ${publishDate}`, 'success');
            this.loadAnnonces();
        }
    }

    duplicateAnnonce(id) {
        const annonce = window.adminSystem.getAnnonces().find(a => a.id === id);
        if (!annonce) return;

        const duplicated = {
            title: `${annonce.title} (copie)`,
            content: annonce.content,
            status: 'draft',
            date: new Date().toISOString().split('T')[0]
        };

        const created = window.adminSystem.addAnnonce(duplicated);
        if (created) {
            this.showNotification(`Annonce "${created.title}" dupliquée avec succès!`, 'success');
            this.loadAnnonces();
        }
    }

    exportAnnonces() {
        const annonces = window.adminSystem.getAnnonces();
        const csv = this.convertToCSV(annonces);
        this.downloadCSV(csv, 'annonces.csv');
    }

    convertToCSV(data) {
        const headers = ['Titre', 'Contenu', 'Statut', 'Date', 'Auteur'];
        const csvContent = [
            headers.join(','),
            ...data.map(item => [
                `"${item.title}"`,
                `"${item.content.replace(/"/g, '""')}"`,
                `"${this.getStatusText(item.status)}"`,
                `"${item.date}"`,
                `"${item.author}"`
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

    // Recherche et filtrage
    searchAnnonces(query) {
        const annonces = window.adminSystem.getAnnonces();
        const filtered = annonces.filter(annonce => 
            annonce.title.toLowerCase().includes(query.toLowerCase()) ||
            annonce.content.toLowerCase().includes(query.toLowerCase())
        );
        this.renderAnnonces(filtered);
    }

    filterByStatus(status) {
        const annonces = window.adminSystem.getAnnonces();
        const filtered = status ? annonces.filter(a => a.status === status) : annonces;
        this.renderAnnonces(filtered);
    }
}

// Initialiser le gestionnaire d'annonces
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('annonces.html')) {
        window.annoncesManager = new AnnoncesManager();
    }
});

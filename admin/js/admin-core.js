// Système d'administration dynamique - Linekode
class AdminSystem {
    constructor() {
        this.initializeStorage();
        this.loadSampleData();
        this.setupClientSync();
    }

    // Initialiser le stockage local
    initializeStorage() {
        if (!localStorage.getItem('linekode_admin_data')) {
            const initialData = {
                inscriptions: [],
                annonces: [],
                messages: [],
                settings: {
                    siteName: 'Linekode',
                    adminEmail: 'admin@linekode.sn',
                    currency: 'FCFA'
                },
                stats: {
                    totalInscriptions: 0,
                    totalRevenue: 0,
                    conversionRate: 0
                }
            };
            localStorage.setItem('linekode_admin_data', JSON.stringify(initialData));
        }
    }

    // Charger les données d'exemple
    loadSampleData() {
        const data = this.getData();
        
        // Ne charger les données d'exemple que si tout est vide
        const hasData = data.inscriptions.length > 0 || data.annonces.length > 0 || data.messages.length > 0;
        
        if (!hasData) {
            // Commencer avec des compteurs à zéro
            data.inscriptions = [];
            data.annonces = [];
            data.messages = [];
            data.stats = {
                totalInscriptions: 0,
                totalRevenue: 0,
                conversionRate: 0
            };
            
            // Ajouter quelques annonces d'exemple (optionnel)
            data.annonces = [
                {
                    id: 1,
                    title: 'Bienvenue sur Linekode',
                    content: 'Nous sommes ravis de vous accueillir dans notre école de formation en développement web.',
                    status: 'published',
                    date: new Date().toISOString().split('T')[0],
                    author: 'Admin'
                }
            ];
            
            this.saveData(data);
        }
    }

    // Obtenir les données
    getData() {
        return JSON.parse(localStorage.getItem('linekode_admin_data') || '{}');
    }

    // Sauvegarder les données
    saveData(data) {
        localStorage.setItem('linekode_admin_data', JSON.stringify(data));
        this.updateStats();
    }

    // Mettre à jour les statistiques
    updateStats() {
        const data = this.getData();
        data.stats.totalInscriptions = data.inscriptions.length;
        data.stats.totalRevenue = data.inscriptions
            .filter(i => i.status === 'confirmed')
            .length * 20000; // 20K FCFA par inscription
        data.stats.conversionRate = data.inscriptions.length > 0 
            ? Math.round((data.inscriptions.filter(i => i.status === 'confirmed').length / data.inscriptions.length) * 100)
            : 0;
        this.saveData(data);
    }

    // === GESTION DES INSCRIPTIONS ===
    
    addInscription(inscription) {
        const data = this.getData();
        inscription.id = Date.now();
        inscription.date = new Date().toISOString().split('T')[0];
        inscription.status = 'new';
        data.inscriptions.push(inscription);
        this.saveData(data);
        return inscription;
    }

    updateInscription(id, updates) {
        const data = this.getData();
        const index = data.inscriptions.findIndex(i => i.id === id);
        if (index !== -1) {
            data.inscriptions[index] = { ...data.inscriptions[index], ...updates };
            this.saveData(data);
            return data.inscriptions[index];
        }
        return null;
    }

    deleteInscription(id) {
        const data = this.getData();
        data.inscriptions = data.inscriptions.filter(i => i.id !== id);
        this.saveData(data);
        return true;
    }

    getInscriptions(filters = {}) {
        const data = this.getData();
        let inscriptions = [...data.inscriptions];
        
        if (filters.status) {
            inscriptions = inscriptions.filter(i => i.status === filters.status);
        }
        if (filters.formation) {
            inscriptions = inscriptions.filter(i => i.formation === filters.formation);
        }
        if (filters.search) {
            const search = filters.search.toLowerCase();
            inscriptions = inscriptions.filter(i => 
                i.name.toLowerCase().includes(search) ||
                i.email.toLowerCase().includes(search)
            );
        }
        
        return inscriptions.sort((a, b) => new Date(b.date) - new Date(a.date));
    }

    // === GESTION DES ANNONCES ===
    
    addAnnonce(annonce) {
        const data = this.getData();
        annonce.id = Date.now();
        annonce.date = new Date().toISOString().split('T')[0];
        annonce.author = 'Admin';
        data.annonces.push(annonce);
        this.saveData(data);
        return annonce;
    }

    updateAnnonce(id, updates) {
        const data = this.getData();
        const index = data.annonces.findIndex(a => a.id === id);
        if (index !== -1) {
            data.annonces[index] = { ...data.annonces[index], ...updates };
            this.saveData(data);
            return data.annonces[index];
        }
        return null;
    }

    deleteAnnonce(id) {
        const data = this.getData();
        data.annonces = data.annonces.filter(a => a.id !== id);
        this.saveData(data);
        return true;
    }

    getAnnonces() {
        const data = this.getData();
        return [...data.annonces].sort((a, b) => new Date(b.date) - new Date(a.date));
    }

    // === GESTION DES MESSAGES ===
    
    addMessage(message) {
        const data = this.getData();
        message.id = Date.now();
        message.date = new Date().toLocaleString('fr-FR');
        message.read = false;
        data.messages.push(message);
        this.saveData(data);
        return message;
    }

    markMessageAsRead(id) {
        const data = this.getData();
        const message = data.messages.find(m => m.id === id);
        if (message) {
            message.read = true;
            this.saveData(data);
        }
        return message;
    }

    deleteMessage(id) {
        const data = this.getData();
        data.messages = data.messages.filter(m => m.id !== id);
        this.saveData(data);
        return true;
    }

    getMessages(unreadOnly = false) {
        const data = this.getData();
        let messages = [...data.messages];
        if (unreadOnly) {
            messages = messages.filter(m => !m.read);
        }
        return messages.sort((a, b) => new Date(b.date) - new Date(a.date));
    }

    // === STATISTIQUES ===
    
    getStats() {
        return this.getData().stats;
    }

    getFormationStats() {
        const data = this.getData();
        const formations = {};
        
        data.inscriptions.forEach(inscription => {
            if (!formations[inscription.formation]) {
                formations[inscription.formation] = {
                    name: inscription.formation,
                    count: 0,
                    revenue: 0
                };
            }
            formations[inscription.formation].count++;
            if (inscription.status === 'confirmed') {
                formations[inscription.formation].revenue += 20000;
            }
        });
        
        return Object.values(formations).sort((a, b) => b.count - a.count);
    }

    getMonthlyStats() {
        const data = this.getData();
        const monthly = {};
        
        // Initialiser les 6 derniers mois
        for (let i = 5; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            const monthKey = date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'short' });
            monthly[monthKey] = { inscriptions: 0, revenue: 0 };
        }
        
        data.inscriptions.forEach(inscription => {
            const date = new Date(inscription.date);
            const monthKey = date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'short' });
            if (monthly[monthKey]) {
                monthly[monthKey].inscriptions++;
                if (inscription.status === 'confirmed') {
                    monthly[monthKey].revenue += 20000;
                }
            }
        });
        
        return monthly;
    }

    // === SYNCHRONISATION CLIENT ===
    
    setupClientSync() {
        // Importer les données du client au chargement
        this.importClientData();
        
        // Écouter les événements de synchronisation
        window.addEventListener('adminSync', (event) => {
            this.handleClientSync(event.detail);
        });
        
        // Configurer la synchronisation automatique
        setInterval(() => {
            this.processSyncQueue();
        }, 5000);
    }
    
    importClientData() {
        // Importer les inscriptions du client
        const clientInscriptions = JSON.parse(localStorage.getItem('linekode_inscriptions') || '[]');
        clientInscriptions.forEach(inscription => {
            if (!this.getInscriptions().find(i => i.id === inscription.id)) {
                this.addInscription(inscription);
            }
        });
        
        // Importer les messages du client
        const clientMessages = JSON.parse(localStorage.getItem('linekode_messages') || '[]');
        clientMessages.forEach(message => {
            if (!this.getMessages().find(m => m.id === message.id)) {
                this.addMessage(message);
            }
        });
        
        // Nettoyer les anciennes données du client
        if (clientInscriptions.length > 0) {
            localStorage.removeItem('linekode_inscriptions');
        }
        if (clientMessages.length > 0) {
            localStorage.removeItem('linekode_messages');
        }
    }
    
    handleClientSync(detail) {
        console.log('Synchronisation client reçue:', detail);
        
        if (detail.key === 'linekode_inscriptions') {
            detail.data.forEach(inscription => {
                if (!this.getInscriptions().find(i => i.id === inscription.id)) {
                    this.addInscription(inscription);
                }
            });
        } else if (detail.key === 'linekode_messages') {
            detail.data.forEach(message => {
                if (!this.getMessages().find(m => m.id === message.id)) {
                    this.addMessage(message);
                }
            });
        }
        
        // Afficher une notification
        if (typeof showNotification === 'function') {
            showNotification(`Nouvelles données synchronisées: ${detail.key}`, 'success');
        }
    }
    
    processSyncQueue() {
        const syncQueue = JSON.parse(localStorage.getItem('adminSyncQueue') || '[]');
        
        if (syncQueue.length > 0) {
            syncQueue.forEach(item => {
                this.handleClientSync(item);
            });
            
            // Vider la file d'attente
            localStorage.removeItem('adminSyncQueue');
        }
    }

    // === EXPORT ===
    
    exportData() {
        const data = this.getData();
        const dataStr = JSON.stringify(data, null, 2);
        const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
        
        const exportFileDefaultName = `linekode-data-${new Date().toISOString().split('T')[0]}.json`;
        
        const linkElement = document.createElement('a');
        linkElement.setAttribute('href', dataUri);
        linkElement.setAttribute('download', exportFileDefaultName);
        linkElement.click();
    }

    importData(jsonData) {
        try {
            const data = JSON.parse(jsonData);
            this.saveData(data);
            return true;
        } catch (error) {
            console.error('Erreur d\'import:', error);
            return false;
        }
    }
}

// Initialiser le système d'administration
window.adminSystem = new AdminSystem();

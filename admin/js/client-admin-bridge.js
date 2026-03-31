// Pont entre le client et l'administration - Linekode
// Synchronise les données du site public avec l'administration

class ClientAdminBridge {
    constructor() {
        this.init();
        this.setupSyncListener();
        this.setupDataMigration();
    }

    init() {
        // Vérifier si nous sommes sur une page client
        if (this.isClientPage()) {
            this.setupClientHandlers();
        }
        
        // Vérifier si nous sommes sur une page admin
        if (this.isAdminPage()) {
            this.setupAdminHandlers();
        }
    }

    isClientPage() {
        return !window.location.pathname.includes('/admin/');
    }

    isAdminPage() {
        return window.location.pathname.includes('/admin/');
    }

    setupClientHandlers() {
        // Surveiller les changements dans localStorage
        this.watchLocalStorage();
        
        // Configurer les formulaires pour la synchronisation
        this.setupFormSync();
    }

    setupAdminHandlers() {
        // Importer les données du client lors du chargement
        this.importClientData();
        
        // Configurer l'écoute des nouvelles données
        this.setupAdminSync();
    }

    watchLocalStorage() {
        const originalSetItem = localStorage.setItem;
        localStorage.setItem = (key, value) => {
            const result = originalSetItem.call(localStorage, key, value);
            
            // Synchroniser avec l'administration si nécessaire
            if (key === 'linekode_inscriptions' || key === 'linekode_messages') {
                this.syncToAdmin(key, JSON.parse(value));
            }
            
            return result;
        };
    }

    setupFormSync() {
        // Synchronisation du formulaire d'inscription
        const inscriptionForm = document.getElementById('inscriptionForm');
        if (inscriptionForm) {
            const originalSubmit = inscriptionForm.onsubmit;
            inscriptionForm.addEventListener('submit', (e) => {
                this.handleInscriptionSubmit(e);
            });
        }

        // Synchronisation du formulaire de contact
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.onclick = (e) => this.handleContactSubmit(e);
            }
        }
    }

    handleInscriptionSubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const inscription = {
            id: Date.now(),
            name: `${formData.get('prenom')} ${formData.get('nom')}`,
            firstName: formData.get('prenom'),
            lastName: formData.get('nom'),
            email: formData.get('email'),
            phone: formData.get('telephone'),
            age: formData.get('age'),
            formation: this.getFormationName(formData.get('formation')),
            niveau: formData.get('niveau'),
            motivation: formData.get('motivation'),
            newsletter: formData.get('newsletter') === 'on',
            status: 'new',
            date: new Date().toISOString().split('T')[0],
            source: 'site_public'
        };

        // Sauvegarder dans localStorage client
        let inscriptions = JSON.parse(localStorage.getItem('linekode_inscriptions') || '[]');
        inscriptions.unshift(inscription);
        localStorage.setItem('linekode_inscriptions', JSON.stringify(inscriptions));

        // Synchroniser avec l'administration
        this.syncToAdmin('linekode_inscriptions', inscriptions);

        // Afficher le succès
        this.showInscriptionSuccess(inscription);
    }

    handleContactSubmit(event) {
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;
        const phone = document.getElementById('phone').value || '';

        if (!name || !email || !subject || !message) {
            alert('Veuillez remplir tous les champs obligatoires');
            return;
        }

        const messageData = {
            id: Date.now(),
            sender: name,
            email: email,
            phone: phone,
            subject: subject,
            content: message,
            date: new Date().toLocaleString('fr-FR'),
            read: false,
            source: 'site_public'
        };

        // Sauvegarder dans localStorage client
        let messages = JSON.parse(localStorage.getItem('linekode_messages') || '[]');
        messages.unshift(messageData);
        localStorage.setItem('linekode_messages', JSON.stringify(messages));

        // Synchroniser avec l'administration
        this.syncToAdmin('linekode_messages', messages);

        // Afficher le succès
        this.showContactSuccess(messageData);
    }

    getFormationName(value) {
        const formations = {
            'frontend': 'Développement Web Frontend',
            'backend': 'Développement Web Backend',
            'fullstack': 'Développement Web Complet',
            'react': 'React Avancé',
            'design': 'UI/UX Design',
            'nodejs': 'Node.js Backend'
        };
        return formations[value] || value;
    }

    showInscriptionSuccess(inscription) {
        const successDiv = document.getElementById('inscriptionSuccess');
        const form = document.getElementById('inscriptionForm');
        
        if (successDiv && form) {
            form.style.display = 'none';
            successDiv.classList.add('show');
            
            // Personnaliser le message de succès
            const montantElement = successDiv.querySelector('strong');
            if (montantElement) {
                montantElement.textContent = `Montant à payer : 50 000 FCFA`;
            }
            
            // Envoyer une notification à l'admin
            this.notifyAdmin(inscription, 'inscription');
        }
    }

    showContactSuccess(message) {
        const formSuccess = document.getElementById('formSuccess');
        const contactForm = document.getElementById('contactForm');
        
        if (formSuccess && contactForm) {
            contactForm.style.display = 'none';
            formSuccess.style.display = 'block';
            
            // Envoyer une notification à l'admin
            this.notifyAdmin(message, 'message');
        }
    }

    syncToAdmin(key, data) {
        // Créer un événement personnalisé pour la synchronisation
        const syncEvent = new CustomEvent('adminSync', {
            detail: {
                key: key,
                data: data,
                timestamp: Date.now()
            }
        });
        
        // Envoyer l'événement à toutes les fenêtres/onglets
        window.dispatchEvent(syncEvent);
        
        // Stocker pour synchronisation ultérieure
        this.storeForSync(key, data);
    }

    storeForSync(key, data) {
        const syncData = JSON.parse(localStorage.getItem('adminSyncQueue') || '[]');
        syncData.push({
            key: key,
            data: data,
            timestamp: Date.now()
        });
        localStorage.setItem('adminSyncQueue', JSON.stringify(syncData));
    }

    setupSyncListener() {
        // Écouter les événements de synchronisation
        window.addEventListener('adminSync', (event) => {
            if (this.isAdminPage()) {
                this.handleAdminSync(event.detail);
            }
        });
    }

    handleAdminSync(detail) {
        console.log('Synchronisation admin reçue:', detail);
        
        // Importer les données dans l'administration
        if (typeof window.adminSystem !== 'undefined') {
            if (detail.key === 'linekode_inscriptions') {
                detail.data.forEach(inscription => {
                    if (!window.adminSystem.getInscriptions().find(i => i.id === inscription.id)) {
                        window.adminSystem.addInscription(inscription);
                    }
                });
            } else if (detail.key === 'linekode_messages') {
                detail.data.forEach(message => {
                    if (!window.adminSystem.getMessages().find(m => m.id === message.id)) {
                        window.adminSystem.addMessage(message);
                    }
                });
            }
            
            // Afficher une notification
            if (typeof showNotification === 'function') {
                showNotification(`Nouvelles données synchronisées: ${detail.key}`, 'success');
            }
        }
    }

    importClientData() {
        // Importer les données du client lors du chargement de l'admin
        const inscriptions = JSON.parse(localStorage.getItem('linekode_inscriptions') || '[]');
        const messages = JSON.parse(localStorage.getItem('linekode_messages') || '[]');
        
        if (typeof window.adminSystem !== 'undefined') {
            // Importer les inscriptions
            inscriptions.forEach(inscription => {
                if (!window.adminSystem.getInscriptions().find(i => i.id === inscription.id)) {
                    window.adminSystem.addInscription(inscription);
                }
            });
            
            // Importer les messages
            messages.forEach(message => {
                if (!window.adminSystem.getMessages().find(m => m.id === message.id)) {
                    window.adminSystem.addMessage(message);
                }
            });
            
            // Nettoyer le localStorage client
            this.cleanupClientData();
        }
    }

    cleanupClientData() {
        // Optionnel: nettoyer les données du client après import
        // localStorage.removeItem('linekode_inscriptions');
        // localStorage.removeItem('linekode_messages');
    }

    setupAdminSync() {
        // Configurer la synchronisation automatique pour l'admin
        setInterval(() => {
            this.processSyncQueue();
        }, 5000); // Vérifier toutes les 5 secondes
    }

    processSyncQueue() {
        const syncQueue = JSON.parse(localStorage.getItem('adminSyncQueue') || '[]');
        
        if (syncQueue.length > 0 && typeof window.adminSystem !== 'undefined') {
            syncQueue.forEach(item => {
                this.handleAdminSync(item);
            });
            
            // Vider la file d'attente
            localStorage.removeItem('adminSyncQueue');
        }
    }

    setupDataMigration() {
        // Migration des anciennes données si nécessaire
        this.migrateOldData();
    }

    migrateOldData() {
        // Vérifier s'il y a des anciennes données à migrer
        const oldInscriptions = JSON.parse(localStorage.getItem('inscriptions') || '[]');
        const oldMessages = JSON.parse(localStorage.getItem('messages') || '[]');
        
        if (oldInscriptions.length > 0 && !localStorage.getItem('linekode_inscriptions')) {
            localStorage.setItem('linekode_inscriptions', JSON.stringify(oldInscriptions));
            localStorage.removeItem('inscriptions');
        }
        
        if (oldMessages.length > 0 && !localStorage.getItem('linekode_messages')) {
            localStorage.setItem('linekode_messages', JSON.stringify(oldMessages));
            localStorage.removeItem('messages');
        }
    }

    notifyAdmin(data, type) {
        // Envoyer une notification à l'administrateur
        const notification = {
            type: type,
            data: data,
            timestamp: new Date().toISOString(),
            message: type === 'inscription' ? 
                `Nouvelle inscription: ${data.name}` : 
                `Nouveau message de: ${data.sender}`
        };
        
        // Sauvegarder la notification
        const notifications = JSON.parse(localStorage.getItem('adminNotifications') || '[]');
        notifications.unshift(notification);
        localStorage.setItem('adminNotifications', JSON.stringify(notifications));
        
        // Afficher une alerte immédiate si l'admin est connecté
        if (this.isAdminPage() && typeof showNotification === 'function') {
            showNotification(notification.message, 'success');
        }
    }

    // Validation améliorée
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    validatePhone(phone) {
        const re = /^(\+221)?[234567]\d{8}$/;
        return re.test(phone.replace(/\s/g, ''));
    }

    validateForm(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                this.showFieldError(input, 'Ce champ est requis');
            } else if (input.type === 'email' && !this.validateEmail(input.value)) {
                isValid = false;
                this.showFieldError(input, 'Email invalide');
            } else if (input.type === 'tel' && input.value && !this.validatePhone(input.value)) {
                isValid = false;
                this.showFieldError(input, 'Numéro de téléphone invalide');
            } else {
                this.clearFieldError(input);
            }
        });
        
        return isValid;
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        
        const error = document.createElement('div');
        error.className = 'field-error';
        error.textContent = message;
        error.style.cssText = `
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        `;
        
        field.style.borderColor = '#e74c3c';
        field.parentNode.appendChild(error);
    }

    clearFieldError(field) {
        field.style.borderColor = '';
        const error = field.parentNode.querySelector('.field-error');
        if (error) {
            error.remove();
        }
    }
}

// Initialiser le pont client-admin
document.addEventListener('DOMContentLoaded', function() {
    window.clientAdminBridge = new ClientAdminBridge();
});

// Exporter pour utilisation globale
window.ClientAdminBridge = ClientAdminBridge;

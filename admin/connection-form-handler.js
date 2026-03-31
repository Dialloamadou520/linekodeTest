// Gestionnaire de connexion pour les formulaires du site public
// Connecte les formulaires d'inscription et de contact à l'administration

class ConnectionFormHandler {
    constructor() {
        this.init();
    }

    init() {
        this.setupInscriptionForm();
        this.setupContactForm();
        this.setupMessageListener();
    }

    setupInscriptionForm() {
        const inscriptionForm = document.querySelector('#inscriptionForm');
        if (inscriptionForm) {
            inscriptionForm.addEventListener('submit', (e) => this.handleInscription(e));
        }
    }

    setupContactForm() {
        const contactForm = document.querySelector('#contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => this.handleContact(e));
        }
    }

    handleInscription(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const inscription = {
            name: formData.get('name') || formData.get('fullName'),
            email: formData.get('email'),
            phone: formData.get('phone') || formData.get('telephone'),
            formation: formData.get('formation') || 'Développement Web',
            address: formData.get('address') || '',
            motivation: formData.get('motivation') || 'Inscription via formulaire web'
        };

        // Sauvegarder dans l'administration
        if (typeof window.adminSystem !== 'undefined') {
            const saved = window.adminSystem.addInscription(inscription);
            if (saved) {
                this.showSuccessMessage('Inscription enregistrée avec succès!');
                event.target.reset();
                this.notifyAdmin(saved);
            }
        } else {
            // Fallback: sauvegarder dans localStorage
            this.saveToLocalStorage(inscription, 'inscription');
            this.showSuccessMessage('Inscription enregistrée avec succès!');
            event.target.reset();
        }
    }

    handleContact(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const message = {
            sender: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone') || '',
            subject: formData.get('subject') || 'Message du formulaire de contact',
            content: formData.get('message') || formData.get('content')
        };

        // Sauvegarder dans l'administration
        if (typeof window.adminSystem !== 'undefined') {
            const saved = window.adminSystem.addMessage(message);
            if (saved) {
                this.showSuccessMessage('Message envoyé avec succès!');
                event.target.reset();
                this.notifyAdminMessage(saved);
            }
        } else {
            // Fallback: sauvegarder dans localStorage
            this.saveToLocalStorage(message, 'message');
            this.showSuccessMessage('Message envoyé avec succès!');
            event.target.reset();
        }
    }

    saveToLocalStorage(data, type) {
        const key = `linekode_${type}s`;
        const existing = JSON.parse(localStorage.getItem(key) || '[]');
        data.id = Date.now();
        data.date = new Date().toISOString().split('T')[0];
        data.status = type === 'inscription' ? 'new' : 'unread';
        existing.push(data);
        localStorage.setItem(key, JSON.stringify(existing));
    }

    showSuccessMessage(message) {
        // Créer une notification de succès
        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
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

    notifyAdmin(inscription) {
        // Simuler une notification à l'admin
        console.log('Nouvelle inscription:', inscription);
        
        // Envoyer un email (simulation)
        this.sendAdminEmail(inscription, 'inscription');
    }

    notifyAdminMessage(message) {
        // Simuler une notification à l'admin
        console.log('Nouveau message:', message);
        
        // Envoyer un email (simulation)
        this.sendAdminEmail(message, 'message');
    }

    sendAdminEmail(data, type) {
        // Simuler l'envoi d'email à l'administrateur
        const emailData = {
            to: 'admin@linekode.sn',
            subject: type === 'inscription' ? 
                `Nouvelle inscription: ${data.name}` : 
                `Nouveau message: ${data.subject}`,
            content: this.formatEmailContent(data, type),
            date: new Date().toISOString()
        };

        // Dans un vrai système, vous enverriez cet email via une API
        console.log('Email envoyé à l\'admin:', emailData);

        // Sauvegarder l'email envoyé
        const sentEmails = JSON.parse(localStorage.getItem('sentEmails') || '[]');
        sentEmails.push(emailData);
        localStorage.setItem('sentEmails', JSON.stringify(sentEmails));
    }

    formatEmailContent(data, type) {
        if (type === 'inscription') {
            return `
Nouvelle inscription reçue:

Nom: ${data.name}
Email: ${data.email}
Téléphone: ${data.phone}
Formation: ${data.formation}
Date: ${data.date}
Adresse: ${data.address}
Motivation: ${data.motivation}

Veuillez traiter cette inscription dans le panneau d'administration.
            `;
        } else {
            return `
Nouveau message de contact:

Expéditeur: ${data.sender}
Email: ${data.email}
Téléphone: ${data.phone}
Sujet: ${data.subject}
Date: ${data.date}

Message:
${data.content}

Veuillez répondre à ce message dans le panneau d'administration.
            `;
        }
    }

    setupMessageListener() {
        // Écouter les messages de l'administration
        window.addEventListener('message', (event) => {
            if (event.data.type === 'adminConnected') {
                console.log('Administration connectée');
                this.syncData();
            }
        });
    }

    syncData() {
        // Synchroniser les données du localStorage avec l'administration
        if (typeof window.adminSystem !== 'undefined') {
            // Synchroniser les inscriptions
            const inscriptions = JSON.parse(localStorage.getItem('linekode_inscriptions') || '[]');
            inscriptions.forEach(inscription => {
                if (!window.adminSystem.getInscriptions().find(i => i.id === inscription.id)) {
                    window.adminSystem.addInscription(inscription);
                }
            });

            // Synchroniser les messages
            const messages = JSON.parse(localStorage.getItem('linekode_messages') || '[]');
            messages.forEach(message => {
                if (!window.adminSystem.getMessages().find(m => m.id === message.id)) {
                    window.adminSystem.addMessage(message);
                }
            });

            // Nettoyer le localStorage
            localStorage.removeItem('linekode_inscriptions');
            localStorage.removeItem('linekode_messages');
        }
    }

    // Validation des formulaires
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    validatePhone(phone) {
        const re = /^(\+221)?[234567]\d{8}$/;
        return re.test(phone.replace(/\s/g, ''));
    }

    setupValidation() {
        // Ajouter la validation aux formulaires
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input[type="email"], input[type="tel"]');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    if (input.type === 'email' && input.value && !this.validateEmail(input.value)) {
                        this.showFieldError(input, 'Veuillez entrer une adresse email valide');
                    } else if (input.type === 'tel' && input.value && !this.validatePhone(input.value)) {
                        this.showFieldError(input, 'Veuillez entrer un numéro de téléphone valide');
                    } else {
                        this.clearFieldError(input);
                    }
                });
            });
        });
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        
        const error = document.createElement('div');
        error.className = 'field-error';
        error.textContent = message;
        error.style.cssText = `
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
        `;
        
        field.style.borderColor = '#ef4444';
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

// Initialiser le gestionnaire de connexion
document.addEventListener('DOMContentLoaded', function() {
    // Ne pas initialiser sur les pages admin
    if (!window.location.pathname.includes('/admin/')) {
        window.connectionFormHandler = new ConnectionFormHandler();
    }
});

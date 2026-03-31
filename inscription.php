<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Linekode Sénégal">
    <meta name="keywords" content="inscription, formation, développement web, Sénégal, Saint-Louis, Linekode">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inscrivez-vous à nos formations en développement web - Linekode">
    <title>Inscription - Linekode</title>
    
    <!-- Favicon Linekode -->
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/favicon.ico">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://linekode.com/inscription.php">
    <meta property="og:title" content="Inscription - Linekode Sénégal">
    <meta property="og:description" content="Inscrivez-vous à nos formations en développement web. Démarrez votre carrière dans le numérique.">
    <meta property="og:image" content="https://linekode.com/images/logo-linekode.png">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://linekode.com/inscription.php">
    <meta property="twitter:title" content="Inscription - Linekode Sénégal">
    <meta property="twitter:description" content="Inscrivez-vous à nos formations en développement web. Démarrez votre carrière dans le numérique.">
    <meta property="twitter:image" content="https://linekode.com/images/logo-linekode.png">
    
    <!-- Feuilles de style -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/whatsapp-styles.css">
    <link rel="stylesheet" href="css/payment-styles.css">
    <link rel="stylesheet" href="css/operator-selection.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .inscription-section {
            padding: 80px 0;
            min-height: calc(100vh - 200px);
        }
        .inscription-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .inscription-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px;
            text-align: center;
        }
        .inscription-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .inscription-header p {
            opacity: 0.9;
        }
        .inscription-body {
            padding: 40px;
        }
        .inscription-form .form-group {
            margin-bottom: 20px;
        }
        .inscription-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        .inscription-form input,
        .inscription-form select,
        .inscription-form textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .inscription-form input:focus,
        .inscription-form select:focus,
        .inscription-form textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .inscription-form .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .inscription-form .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .inscription-form .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-top: 4px;
        }
        .inscription-form .checkbox-group label {
            margin-bottom: 0;
            font-weight: normal;
        }
        .inscription-form .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }
        .inscription-form .form-group.error .error-message {
            display: block;
        }
        .inscription-form .form-group.error input,
        .inscription-form .form-group.error select {
            border-color: #e74c3c;
        }
        .inscription-form button[type="submit"] {
            width: 100%;
            padding: 16px;
            font-size: 1.1rem;
            margin-top: 20px;
        }
        .success-message {
            text-align: center;
            padding: 40px;
            display: none;
        }
        .success-message.show {
            display: block;
        }
        .success-message i {
            font-size: 4rem;
            color: #27ae60;
            margin-bottom: 20px;
        }
        .success-message h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        .success-message p {
            color: var(--text-light);
            margin-bottom: 20px;
        }
        @media (max-width: 600px) {
            .inscription-form .form-row {
                grid-template-columns: 1fr;
            }
            .inscription-header,
            .inscription-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.html" class="logo">
                    <i class="fas fa-code"></i>
                    <span>Linekode</span>
                </a>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="formations.html">Formations</a></li>
                    <li><a href="about.html">À propos</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="inscription.php" class="btn-primary active">Inscrivez-vous</a></li>
                </ul>
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <span class="page-badge"><i class="fas fa-user-plus"></i> Rejoignez-nous</span>
            <div class="inscription-header">
                <h1>Inscription à Linekode</h1>
                <p>Commencez votre parcours vers une carrière en développement web</p>
                <div class="header-contact">
                    <a href="https://wa.me/221711179393?text=Bonjour%20Linekode!%20Je%20souhaite%20m'inscrire%20à%20une%20formation." target="_blank" class="btn-whatsapp">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                    <a href="tel:+221711179393" class="btn-secondary">
                        <i class="fas fa-phone"></i>
                        Appeler
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Inscription Section -->
    <section class="inscription-section">
        <div class="container">
            <div class="inscription-container">
                <div class="inscription-body">
                    <div id="inscriptionSuccess" class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <h3>Inscription réussie !</h3>
                        <p>Merci pour votre inscription. Notre équipe vous contactera dans les 24 heures.</p>
                        <p style="margin-bottom: 20px;"><strong>Montant à payer : 50 000 FCFA</strong></p>
                        <p style="margin-top: 15px; font-size: 0.9rem; color: #666;">
                            Paiement sécurisé via DexpayAfrica (Mobile Money, Carte, Virement)
                        </p>
                        <br>
                        <a href="index.html" class="btn-primary">Retour à l'accueil</a>
                    </div>
                    <!-- Section de paiement -->
                    <div id="operatorSelection" class="operator-selection" style="display: none;">
                        <h3>Choisissez votre mode de paiement</h3>
                        <p class="payment-amount">Montant : <strong>50 000 FCFA</strong></p>
                        <p style="text-align: center; color: #666; margin-bottom: 30px;">
                            Sélectionnez votre opérateur de paiement mobile money
                        </p>
                        
                        <div class="operators-grid">
                            <div class="operator-card" onclick="selectOperator('wave')">
                                <div class="operator-icon wave">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h4>Wave</h4>
                                <p>Paiement instantané</p>
                            </div>
                            
                            <div class="operator-card" onclick="selectOperator('orange_money')">
                                <div class="operator-icon orange">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h4>Orange Money</h4>
                                <p>Paiement sécurisé</p>
                            </div>
                            
                            <div class="operator-card" onclick="selectOperator('mtn')">
                                <div class="operator-icon mtn">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h4>MTN Mobile Money</h4>
                                <p>Paiement rapide</p>
                            </div>
                            
                            <div class="operator-card" onclick="selectOperator('moov')">
                                <div class="operator-icon moov">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h4>Moov Money</h4>
                                <p>Paiement simple</p>
                            </div>
                        </div>
                        
                        <div id="operatorLoading" style="display: none; text-align: center; margin-top: 20px;">
                            <div class="spinner"></div>
                            <p>Connexion à <span id="selectedOperatorName"></span>...</p>
                        </div>
                    </div>

                    <form id="inscriptionForm" class="inscription-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" required>
                                <span class="error-message">Ce champ est requis</span>
                            </div>
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" required>
                                <span class="error-message">Ce champ est requis</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email_inscription">Email *</label>
                            <input type="email" id="email_inscription" name="email" required>
                            <span class="error-message">Email invalide</span>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telephone">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" required>
                                <span class="error-message">Ce champ est requis</span>
                            </div>
                            <div class="form-group">
                                <label for="age">Âge</label>
                                <input type="number" id="age" name="age" min="16" max="99">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="niveau">Niveau actuel *</label>
                            <select id="niveau" name="niveau" required>
                                <option value="">Sélectionnez votre niveau</option>
                                <option value="debutant">Débutant (aucune expérience)</option>
                                <option value="intermediaire">Intermédiaire (quelques bases)</option>
                                <option value="avance">Avancé (expérience significative)</option>
                            </select>
                            <span class="error-message">Veuillez sélectionner votre niveau</span>
                        </div>
                        
                        <div class="form-group">
                            <label for="motivation">Pourquoi souhaitez-vous suivre cette formation ?</label>
                            <textarea id="motivation" name="motivation" rows="3" placeholder="Parlez-nous de vos objectifs..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="accepte_conditions" name="accepte_conditions" required>
                                <label for="accepte_conditions">
                                    J'accepte les <a href="#" target="_blank">conditions générales</a> et la <a href="#" target="_blank">politique de confidentialité</a> *
                                </label>
                            </div>
                            <span class="error-message">Vous devez accepter les conditions</span>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="newsletter" name="newsletter">
                                <label for="newsletter">
                                    Je souhaite recevoir des informations sur les formations et actualités de Linekode
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check"></i>
                            Valider mon inscription
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-code"></i>
                        <span>Linekode</span>
                    </div>
                    <p>Votre partenaire pour une carrière réussie dans le développement web. Formations de qualité et conception de sites web et d'applications mobiles.</p>
                </div>
                <div class="footer-col">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="index.html">Accueil</a></li>
                        <li><a href="formations.html">Formations</a></li>
                        <li><a href="about.html">À propos</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> linekodesn@gmail.com</li>
                        <li><i class="fas fa-phone"></i> +221 71 117 93 93</li>
                        <li><i class="fas fa-map-marker-alt"></i> Saint-Louis, Sénégal</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Suivez-nous</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Linekode. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js?v=2"></script>
    <script>
        // Pré-remplir la formation depuis l'URL
        document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validation simple
            const requiredFields = ['prenom', 'nom', 'email_inscription', 'telephone'];
            let isValid = true;
            
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field || !field.value.trim()) {
                    isValid = false;
                    if (field) {
                        field.style.borderColor = 'red';
                    }
                }
            });
            
            if (!isValid) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            const formData = new FormData(this);
            const inscription = {
                id: Date.now(),
                name: `${formData.get('prenom')} ${formData.get('nom')}`,
                firstName: formData.get('prenom'),
                lastName: formData.get('nom'),
                email: formData.get('email'),
                phone: formData.get('telephone'),
                age: formData.get('age'),
                formation: 'Non spécifiée',
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
            
            // Stocker l'ID de l'inscription pour le paiement
            window.currentInscriptionId = inscription.id;
            
            // Masquer le formulaire
            this.style.display = 'none';
            
            // Stocker les données pour le paiement
            window.paymentData = {
                phoneNumber: formData.get('telephone'),
                email: formData.get('email'),
                name: formData.get('prenom') + ' ' + formData.get('nom')
            };
            
            // Log pour vérifier les données
            console.log('✅ Données stockées:', window.paymentData);
            
            // Afficher un message de chargement
            const loadingMsg = document.createElement('div');
            loadingMsg.id = 'paymentLoading';
            loadingMsg.style.cssText = 'text-align: center; padding: 40px; font-size: 18px;';
            loadingMsg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirection vers la page de paiement DexpayAfrica...';
            this.parentElement.appendChild(loadingMsg);
            
            // Appeler directement l'API DexpayAfrica
            processDexpayPaymentDirect(formData.get('telephone'), formData.get('email'));
        });
    </script>
    <script>
        // Fonction pour afficher la modal de paiement DexpayAfrica
        function showPaymentModal() {
            // Récupérer les données du formulaire
            const form = document.getElementById('inscriptionForm');
            const formData = new FormData(form);
            
            const phoneNumber = formData.get('telephone');
            const email = formData.get('email');
            
            // Afficher la modal avec formulaire de paiement
            const modal = document.createElement('div');
            modal.id = 'dexpayPaymentModal';
            modal.className = 'payment-modal';
            modal.innerHTML = `
                <div class="payment-modal-content">
                    <div class="payment-modal-header">
                        <h3>💳 Paiement DexpayAfrica</h3>
                        <button class="payment-modal-close" onclick="hideDexpayModal()">&times;</button>
                    </div>
                    <div class="payment-modal-body">
                        <h4>Choisissez votre méthode de paiement</h4>
                        
                        <div class="payment-methods-grid">
                            <div class="payment-method-card" onclick="selectPaymentMethod('mobile_money')">
                                <div class="payment-method-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h4>Mobile Money</h4>
                                <p>Orange Money, Wave, MTN, Moov</p>
                            </div>
                            
                            <div class="payment-method-card" onclick="selectPaymentMethod('card')">
                                <div class="payment-method-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <h4>Carte de Crédit</h4>
                                <p>Visa, Mastercard, American Express</p>
                            </div>
                            
                            <div class="payment-method-card" onclick="selectPaymentMethod('bank_transfer')">
                                <div class="payment-method-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <h4>Virement Bancaire</h4>
                                <p>Virement vers compte DexpayAfrica</p>
                            </div>
                        </div>
                        
                        <div class="payment-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="paymentPhoneNumber">Numéro de téléphone *</label>
                                    <input type="tel" id="paymentPhoneNumber" value="${phoneNumber || ''}" placeholder="+221 XX XXX XX XX" required>
                                </div>
                                <div class="form-group">
                                    <label for="paymentEmail">Email</label>
                                    <input type="email" id="paymentEmail" value="${email || ''}" placeholder="votre@email.com">
                                </div>
                            </div>
                            
                            <div class="payment-amount-display">
                                <h4>Montant à payer</h4>
                                <div class="amount">50 000 XOF</div>
                            </div>
                            
                            <div class="payment-actions">
                                <button type="button" class="btn-pay" onclick="processDexpayPayment()">
                                    <i class="fas fa-lock"></i>
                                    Payer maintenant
                                </button>
                                <button type="button" class="btn-cancel" onclick="hideDexpayModal()">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="payment-modal-backdrop"></div>
            `;
            
            document.body.appendChild(modal);
            
            // Ajouter les styles pour la modal Dexpay
            if (!document.getElementById('dexpayModalStyles')) {
                const styles = document.createElement('style');
                styles.id = 'dexpayModalStyles';
                styles.textContent = `
                    .payment-methods-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                        gap: 15px;
                        margin: 20px 0;
                    }
                    
                    .payment-method-card {
                        padding: 20px;
                        border: 2px solid #e0e0e0;
                        border-radius: 12px;
                        text-align: center;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }
                    
                    .payment-method-card:hover {
                        border-color: #FF6B35;
                        transform: translateY(-5px);
                        box-shadow: 0 10px 25px rgba(255, 107, 53, 0.1);
                    }
                    
                    .payment-method-card.selected {
                        border-color: #FF6B35;
                        background: #FFF5F5;
                    }
                    
                    .payment-method-icon {
                        font-size: 2.5rem;
                        margin-bottom: 15px;
                        color: #666;
                    }
                    
                    .payment-method-card:hover .payment-method-icon,
                    .payment-method-card.selected .payment-method-icon {
                        color: #FF6B35;
                    }
                    
                    .payment-method-card h4 {
                        margin-bottom: 8px;
                        color: #333;
                    }
                    
                    .payment-method-card p {
                        font-size: 0.85rem;
                        color: #666;
                        margin: 0;
                    }
                    
                    .payment-form {
                        background: #f8f9fa;
                        padding: 25px;
                        border-radius: 12px;
                        margin: 20px 0;
                    }
                    
                    .payment-form .form-row {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 15px;
                        margin-bottom: 15px;
                    }
                    
                    .payment-form label {
                        display: block;
                        margin-bottom: 5px;
                        font-weight: 600;
                        color: #333;
                    }
                    
                    .payment-form input {
                        width: 100%;
                        padding: 10px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-size: 1rem;
                    }
                    
                    .payment-form input:focus {
                        outline: none;
                        border-color: #FF6B35;
                    }
                    
                    .payment-amount-display {
                        text-align: center;
                        padding: 20px;
                        background: white;
                        border-radius: 8px;
                        border: 2px solid #e0e0e0;
                        margin-bottom: 20px;
                    }
                    
                    .payment-amount-display h4 {
                        margin-bottom: 10px;
                        color: #666;
                    }
                    
                    .payment-amount-display .amount {
                        font-size: 2rem;
                        font-weight: 700;
                        color: #FF6B35;
                    }
                    
                    .payment-actions {
                        display: flex;
                        gap: 15px;
                    }
                    
                    .btn-pay {
                        flex: 1;
                        background: #FF6B35;
                        color: white;
                        border: none;
                        padding: 15px 20px;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }
                    
                    .btn-pay:hover {
                        background: #E55A24;
                        transform: translateY(-2px);
                    }
                    
                    .btn-cancel {
                        flex: 1;
                        background: #6c757d;
                        color: white;
                        border: none;
                        padding: 15px 20px;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }
                    
                    .btn-cancel:hover {
                        background: #5a6268;
                    }
                    
                    @media (max-width: 600px) {
                        .payment-methods-grid {
                            grid-template-columns: 1fr;
                        }
                        
                        .payment-form .form-row {
                            grid-template-columns: 1fr;
                        }
                    }
                `;
                document.head.appendChild(styles);
            }
        }
        
        // Fonction de paiement direct avec lien vers l'opérateur
        async function selectOperator(operator) {
            const operatorNames = {
                'wave': 'Wave',
                'orange_money': 'Orange Money',
                'mtn': 'MTN Mobile Money',
                'moov': 'Moov Money'
            };
            
            // Masquer la grille d'opérateurs et afficher le chargement
            document.querySelector('.operators-grid').style.display = 'none';
            const loadingDiv = document.getElementById('operatorLoading');
            loadingDiv.style.display = 'block';
            document.getElementById('selectedOperatorName').textContent = operatorNames[operator];
            
            try {
                // Vérifier que window.paymentData existe
                console.log('🔍 Vérification window.paymentData:', window.paymentData);
                
                if (!window.paymentData) {
                    throw new Error('Données de paiement non trouvées. Veuillez remplir le formulaire à nouveau.');
                }
                
                // Créer le lien de paiement direct
                const paymentPayload = {
                    operator: operator,
                    amount: 50000,
                    description: 'Inscription Formation Linekode',
                    customer_phone: window.paymentData.phoneNumber,
                    customer_email: window.paymentData.email
                };
                
                console.log('📤 Création du lien de paiement');
                console.log('📤 Opérateur:', operatorNames[operator]);
                console.log('📤 Montant: 50000 XOF');
                
                const response = await fetch('api/create-payment-link.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(paymentPayload)
                });
                
                const result = await response.json();
                console.log('📥 Réponse:', result);
                
                if (!result.success) {
                    throw new Error(result.error || 'Erreur lors de la création du lien de paiement');
                }
                
                // Sauvegarder le paiement dans localStorage
                const payment = {
                    reference: result.reference,
                    amount: 50000,
                    method: 'mobile_money',
                    operator: operator,
                    operator_name: operatorNames[operator],
                    status: 'pending',
                    created_at: new Date().toISOString(),
                    inscription_id: window.currentInscriptionId,
                    payment_url: result.payment_url
                };
                
                let payments = JSON.parse(localStorage.getItem('linekode_payments') || '[]');
                payments.unshift(payment);
                localStorage.setItem('linekode_payments', JSON.stringify(payments));
                
                console.log('✅ Lien de paiement créé:', result.payment_url);
                console.log('✅ Référence:', result.reference);
                console.log('✅ Redirection vers', operatorNames[operator]);
                
                // Rediriger vers l'URL de paiement de l'opérateur
                window.location.href = result.payment_url;
                
            } catch (error) {
                console.error('❌ Erreur paiement:', error);
                alert('Erreur lors du paiement: ' + error.message);
                
                // Masquer le chargement et réafficher la grille d'opérateurs
                document.getElementById('operatorLoading').style.display = 'none';
                document.querySelector('.operators-grid').style.display = 'grid';
            }
        }

        async function processDexpayPaymentDirect(phoneNumber, email) {
            // Validation
            if (!phoneNumber || !phoneNumber.trim()) {
                alert('Veuillez entrer votre numéro de téléphone');
                return;
            }
            
            if (!email || !email.trim()) {
                alert('Veuillez entrer votre adresse email');
                return;
            }
            
            // Validation email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Veuillez entrer une adresse email valide');
                return;
            }
            
            try {
                // Créer une session de paiement via notre backend
                const response = await fetch('api/dexpay-checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: 50000,
                        customer_phone: phoneNumber,
                        customer_email: email,
                        description: 'Inscription Formation Linekode',
                        success_url: window.location.origin + window.location.pathname.replace('inscription.html', 'payment-success.php').replace('inscription.php', 'payment-success.php') + '?session_id={CHECKOUT_SESSION_ID}',
                        cancel_url: window.location.origin + window.location.pathname.replace('inscription.html', 'payment-cancelled.php').replace('inscription.php', 'payment-cancelled.php') + '?session_id={CHECKOUT_SESSION_ID}',
                        metadata: {
                            inscription_id: window.currentInscriptionId || Date.now(),
                            source: 'linekode_website'
                        }
                    })
                });
                
                const result = await response.json();
                
                if (result.success && result.checkout_url) {
                    // Sauvegarder le paiement dans localStorage
                    const payment = {
                        session_id: result.session_id,
                        amount: 50000,
                        method: 'dexpay',
                        status: 'pending',
                        created_at: new Date().toISOString(),
                        inscription_id: window.currentInscriptionId || Date.now()
                    };
                    
                    let payments = JSON.parse(localStorage.getItem('linekode_payments') || '[]');
                    payments.unshift(payment);
                    localStorage.setItem('linekode_payments', JSON.stringify(payments));
                    
                    // Rediriger vers DexpayAfrica
                    window.location.href = result.checkout_url;
                } else {
                    throw new Error(result.error || 'Erreur lors de la création de la session de paiement');
                }
                
            } catch (error) {
                console.error('Erreur paiement:', error);
                
                // Masquer le message de chargement
                const loadingMsg = document.getElementById('paymentLoading');
                if (loadingMsg) {
                    loadingMsg.remove();
                }
                
                // Afficher l'erreur
                alert('Erreur lors du paiement: ' + error.message);
                
                // Réafficher le formulaire
                document.getElementById('inscriptionForm').style.display = 'block';
            }
        }
        
        // Fonction pour afficher le message de succès après paiement
        function showPaymentSuccess() {
            // Masquer la section de paiement
            const paymentSection = document.getElementById('paymentSection');
            paymentSection.classList.remove('show');
            
            // Afficher le message d'inscription réussie
            document.getElementById('inscriptionSuccess').classList.add('show');
            
            // Scroller vers le message de succès
            document.getElementById('inscriptionSuccess').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>

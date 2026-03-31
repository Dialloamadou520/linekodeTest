<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Annulé - Linekode</title>
    <meta name="description" content="Votre paiement a été annulé. Vous pouvez réessayer ou nous contacter pour obtenir de l'aide.">
    
    <!-- Favicon Linekode -->
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/favicon.ico">
    
    <!-- Feuilles de style -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/payment-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .cancelled-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
        }
        
        .cancelled-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideInUp 0.6s ease;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cancelled-icon {
            width: 100px;
            height: 100px;
            background: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.6s ease;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .cancelled-icon i {
            font-size: 3rem;
            color: white;
        }
        
        .cancelled-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .cancelled-message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .cancelled-reasons {
            background: #f8d7da;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .reason-title {
            font-weight: 600;
            color: #721c24;
            margin-bottom: 15px;
        }
        
        .reason-list {
            list-style: none;
            padding: 0;
        }
        
        .reason-list li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
            color: #721c24;
        }
        
        .reason-list li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #dc3545;
            font-weight: bold;
        }
        
        .cancelled-actions {
            display: flex;
            gap: 15px;
            flex-direction: column;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-success:hover {
            background: #1e7e34;
            transform: translateY(-2px);
        }
        
        .help-section {
            margin-top: 30px;
            padding: 20px;
            background: #e8f5e8;
            border-radius: 8px;
            border-left: 4px solid #25D366;
        }
        
        .help-section h3 {
            color: #155724;
            margin-bottom: 10px;
        }
        
        .help-options {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        
        .help-option {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            background: white;
            border-radius: 6px;
            text-decoration: none;
            color: #155724;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .help-option:hover {
            background: #d4edda;
            transform: translateY(-2px);
        }
        
        @media (max-width: 600px) {
            .cancelled-card {
                padding: 30px 20px;
            }
            
            .cancelled-title {
                font-size: 1.5rem;
            }
            
            .cancelled-actions {
                flex-direction: column;
            }
            
            .help-options {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="cancelled-container">
        <div class="cancelled-card">
            <div class="cancelled-icon">
                <i class="fas fa-times"></i>
            </div>
            
            <h1 class="cancelled-title">Paiement Annulé</h1>
            
            <p class="cancelled-message">
                Votre paiement a été annulé. Votre inscription n'a pas été finalisée, mais vous pouvez réessayer à tout moment.
            </p>
            
            <div class="cancelled-reasons">
                <h3 class="reason-title">Raisons possibles :</h3>
                <ul class="reason-list">
                    <li>Vous avez fermé la fenêtre de paiement</li>
                    <li>Erreur de connexion internet</li>
                    <li>Carte de crédit refusée</li>
                    <li>Décision de ne pas poursuivre le paiement</li>
                </ul>
            </div>
            
            <div class="cancelled-actions">
                <a href="inscription.php" class="btn-success">
                    <i class="fas fa-redo"></i>
                    Réessayer le paiement
                </a>
                <a href="index.html" class="btn-primary">
                    <i class="fas fa-home"></i>
                    Retour à l'accueil
                </a>
                <a href="contact.html" class="btn-secondary">
                    <i class="fas fa-envelope"></i>
                    Nous contacter
                </a>
            </div>
            
            <div class="help-section">
                <h3>🤝 Besoin d'aide ?</h3>
                <p>Notre équipe est là pour vous aider à finaliser votre inscription.</p>
                <div class="help-options">
                    <a href="https://wa.me/221711179393?text=Bonjour%20Linekode!%20Mon%20paiement%20a%20été%20annulé,%20j'ai%20besoin%20d'aide." target="_blank" class="help-option">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                    <a href="tel:+221711179393" class="help-option">
                        <i class="fas fa-phone"></i>
                        Appeler
                    </a>
                    <a href="mailto:linekodesn@gmail.com" class="help-option">
                        <i class="fas fa-envelope"></i>
                        Email
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Récupérer les paramètres de l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const sessionId = urlParams.get('session_id');
        
        // Si nous avons un session_id, nous pourrions l'utiliser pour des analyses
        if (sessionId) {
            console.log('Session annulée:', sessionId);
        }
        
        // Effacer les données du formulaire d'inscription si nécessaire
        localStorage.removeItem('linekode_inscriptions');
        
        // Animation subtile pour montrer qu'on est là pour aider
        setTimeout(() => {
            const helpSection = document.querySelector('.help-section');
            if (helpSection) {
                helpSection.style.animation = 'pulse 2s ease infinite';
            }
        }, 2000);
        
        // Animation pulse
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.02);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

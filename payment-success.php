<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - Linekode</title>
    <meta name="description" content="Votre paiement a été traité avec succès. Merci pour votre inscription à Linekode.">
    
    <!-- Favicon Linekode -->
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/favicon.ico">
    
    <!-- Feuilles de style -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/payment-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .success-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .success-card {
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
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: #28a745;
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
        
        .success-icon i {
            font-size: 3rem;
            color: white;
        }
        
        .success-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .success-message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .success-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #333;
        }
        
        .detail-value {
            color: #666;
        }
        
        .success-actions {
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
        
        .whatsapp-contact {
            margin-top: 20px;
            padding: 15px;
            background: #e8f5e8;
            border-radius: 8px;
            border-left: 4px solid #25D366;
        }
        
        .whatsapp-contact a {
            color: #25D366;
            text-decoration: none;
            font-weight: 600;
        }
        
        .whatsapp-contact a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .success-card {
                padding: 30px 20px;
            }
            
            .success-title {
                font-size: 1.5rem;
            }
            
            .success-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <h1 class="success-title">Paiement Réussi !</h1>
            
            <p class="success-message">
                Félicitations ! Votre paiement a été traité avec succès et votre inscription à Linekode est maintenant confirmée.
            </p>
            
            <div class="success-details" id="paymentDetails">
                <div class="detail-row">
                    <span class="detail-label">Référence :</span>
                    <span class="detail-value" id="sessionId">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Montant :</span>
                    <span class="detail-value" id="amount">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Méthode :</span>
                    <span class="detail-value" id="method">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date :</span>
                    <span class="detail-value" id="date">-</span>
                </div>
            </div>
            
            <div class="success-actions">
                <a href="index.html" class="btn-primary">
                    <i class="fas fa-home"></i>
                    Retour à l'accueil
                </a>
                <a href="formations.html" class="btn-secondary">
                    <i class="fas fa-graduation-cap"></i>
                    Voir les formations
                </a>
            </div>
            
            <div class="whatsapp-contact">
                <p>📱 <strong>Prochaines étapes :</strong> Notre équipe vous contactera dans les 24 heures pour finaliser votre inscription.</p>
                <p>Besoin d'aide ? <a href="https://wa.me/221711179393?text=Bonjour%20Linekode!%20Je%20viens%20de%20payer%20mon%20inscription." target="_blank">Contactez-nous sur WhatsApp</a></p>
            </div>
        </div>
    </div>
    
    <script>
        // Récupérer les paramètres de l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const sessionId = urlParams.get('session_id');
        
        // Afficher les détails du paiement
        if (sessionId) {
            document.getElementById('sessionId').textContent = sessionId;
            
            // Récupérer les détails du paiement depuis localStorage
            const payments = JSON.parse(localStorage.getItem('linekode_payments') || '[]');
            const payment = payments.find(p => p.session_id === sessionId);
            
            if (payment) {
                document.getElementById('amount').textContent = `${payment.amount} XOF`;
                document.getElementById('method').textContent = getMethodName(payment.method);
                document.getElementById('date').textContent = new Date(payment.created_at).toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
        
        // Obtenir le libellé de la méthode
        function getMethodName(method) {
            const names = {
                'wave': 'Wave',
                'orange_money': 'Orange Money',
                'credit_card': 'Carte de crédit'
            };
            return names[method] || method;
        }
        
        // Effacer les données du formulaire d'inscription
        localStorage.removeItem('linekode_inscriptions');
        
        // Confettis pour célébrer !
        function createConfetti() {
            const confettiCount = 100;
            const colors = ['#28a745', '#007bff', '#ffc107', '#17a2b8'];
            
            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.style.cssText = `
                    position: fixed;
                    width: 10px;
                    height: 10px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    left: ${Math.random() * 100}%;
                    top: -10px;
                    opacity: ${Math.random() + 0.5};
                    transform: rotate(${Math.random() * 360}deg);
                    animation: fall ${Math.random() * 3 + 2}s linear;
                    z-index: 9999;
                `;
                
                document.body.appendChild(confetti);
                
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
        }
        
        // Animation de chute pour les confettis
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Lancer les confettis après 1 seconde
        setTimeout(createConfetti, 1000);
    </script>
</body>
</html>

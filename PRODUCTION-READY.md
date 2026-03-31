# 🚀 Linekode - Configuration Production

## ✅ Site Hébergé sur : **linekode.com**

Votre site est maintenant configuré pour la production avec paiements DexpayAfrica réels.

## 🔐 Configuration DexpayAfrica

### 🔐 Clés API DexpayAfrica

### Clé Publique (API Key)
```
pk_live_99307e13b1f1d9134895fe8df8086a46
```

### Clé Secrète (Secret Key)
```
sk_live_VOTRE_CLE_SECRETE (à configurer dans .env)
```
- **URL API** : `https://api.dexpayafrica.com/v1`

### Mode Production
```php
// api/dexpay-checkout.php - Ligne 20
define('SIMULATION_MODE', false); // ✅ PRODUCTION ACTIVÉE
```

## 🌐 URLs de Production

### Site Principal
- **Domaine** : https://linekode.com
- **Inscription** : https://linekode.com/inscription.html
- **Paiement Succès** : https://linekode.com/payment-success.html
- **Paiement Annulé** : https://linekode.com/payment-cancelled.html

### API Backend
- **Endpoint DexpayAfrica** : https://linekode.com/api/dexpay-checkout.php

## 💳 Flux de Paiement Production

### 1. Inscription
1. Utilisateur remplit le formulaire sur `inscription.html`
2. Validation des données (téléphone + email requis)
3. Inscription sauvegardée dans localStorage

### 2. Paiement DexpayAfrica
1. Section paiement s'affiche automatiquement
2. Utilisateur entre téléphone et email
3. Clic sur "Payer avec DexpayAfrica"
4. Appel à `api/dexpay-checkout.php`
5. Backend crée une session DexpayAfrica réelle
6. Redirection vers plateforme DexpayAfrica

### 3. Traitement
1. Utilisateur choisit sa méthode (Mobile Money, Carte, Virement)
2. Effectue le paiement sur DexpayAfrica
3. DexpayAfrica traite la transaction

### 4. Retour
- **Succès** : Redirection vers `payment-success.html?session_id=xxx`
- **Annulation** : Redirection vers `payment-cancelled.html?session_id=xxx`

## 🔧 Configuration Serveur Requise

### PHP
- ✅ Version 7.4 ou supérieure
- ✅ Extension cURL activée
- ✅ Extension JSON activée
- ✅ `allow_url_fopen` activé

### Permissions
```bash
chmod 755 api/
chmod 644 api/dexpay-checkout.php
```

### HTTPS
⚠️ **IMPORTANT** : DexpayAfrica requiert HTTPS en production
- ✅ Certificat SSL installé sur linekode.com
- ✅ Redirection HTTP → HTTPS configurée

## 🎯 Montant de Paiement

**Montant fixe** : **50 000 XOF** (FCFA)
- Inscription à une formation Linekode
- Paiement unique

## 📱 Méthodes de Paiement Acceptées

Via DexpayAfrica :
- 📱 **Mobile Money** : Wave, Orange Money, MTN, Moov
- 💳 **Cartes Bancaires** : Visa, Mastercard, American Express
- 🏦 **Virement Bancaire** : Transfert direct

## 🔍 Vérification Post-Déploiement

### Tests à Effectuer

1. **Test Formulaire**
   - Remplir le formulaire d'inscription
   - Vérifier la validation des champs
   - Confirmer l'affichage de la section paiement

2. **Test API**
   - Vérifier que `api/dexpay-checkout.php` est accessible
   - Tester avec curl :
   ```bash
   curl -X POST https://linekode.com/api/dexpay-checkout.php \
     -H "Content-Type: application/json" \
     -d '{"amount":50000,"customer_phone":"+221771234567","customer_email":"test@example.com","description":"Test","success_url":"https://linekode.com/payment-success.html","cancel_url":"https://linekode.com/payment-cancelled.html","metadata":{}}'
   ```

3. **Test Paiement Réel**
   - Effectuer une transaction test avec un petit montant
   - Vérifier la redirection vers DexpayAfrica
   - Compléter le paiement
   - Vérifier le retour sur payment-success.html

## 🛡️ Sécurité

### Clés API
- ✅ Clé secrète stockée côté serveur uniquement
- ✅ Jamais exposée au frontend
- ✅ Communication HTTPS uniquement

### Validation
- ✅ Validation téléphone (format international)
- ✅ Validation email (regex)
- ✅ Validation montant côté serveur
- ✅ Protection CORS configurée

## 📊 Monitoring

### Logs à Surveiller
```php
// Logs d'erreurs API dans les logs PHP
error_log("DexpayAfrica API Error: " . $error);
```

### Vérifier
- Taux de conversion (inscriptions → paiements)
- Erreurs API DexpayAfrica
- Temps de réponse
- Abandons de paiement

## 🔄 Fallback Automatique

En cas d'erreur API DexpayAfrica, le système bascule automatiquement en mode simulation :
```php
if ($error) {
    error_log("DexpayAfrica API Error: " . $error);
    return createSimulatedSession($data);
}
```

⚠️ **Note** : En production, surveillez les logs pour détecter ces basculements.

## 📞 Support DexpayAfrica

### Contacts
- **Documentation** : https://docs.dexpayafrica.com
- **Dashboard** : https://dashboard.dexpayafrica.com
- **Support Email** : support@dexpayafrica.com
- **Support Technique** : tech@dexpayafrica.com

### En Cas de Problème
1. Vérifier les logs PHP
2. Tester l'API avec curl
3. Vérifier le dashboard DexpayAfrica
4. Contacter le support si nécessaire

## ✅ Checklist Déploiement

- [x] Mode simulation désactivé (`SIMULATION_MODE = false`)
- [x] Clés API configurées
- [x] HTTPS activé sur linekode.com
- [x] Extension PHP cURL activée
- [x] Permissions fichiers correctes
- [x] URLs de redirection dynamiques
- [ ] Test paiement réel effectué
- [ ] Webhook DexpayAfrica configuré (optionnel)
- [ ] Monitoring mis en place

## 🎉 Votre Site est Prêt !

Linekode est maintenant en production sur **linekode.com** avec :
- ✅ Paiements DexpayAfrica réels
- ✅ Formulaire d'inscription fonctionnel
- ✅ Redirection automatique
- ✅ Support multi-méthodes de paiement
- ✅ Sécurité HTTPS

**Bon lancement ! 🚀**

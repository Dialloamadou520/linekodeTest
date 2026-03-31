# Guide de Configuration DexpayAfrica pour Linekode

## 🎯 Objectif
Ce guide vous aide à configurer votre clé secrète DexpayAfrica pour activer les paiements réels dans votre système Linekode.

## 📋 Prérequis
- Compte DexpayAfrica actif (business ou entreprise)
- Clé secrète DexpayAfrica (commençant par `sk_live_`)
- Accès au dashboard DexpayAfrica
- Site Linekode déjà déployé

## 🔍 Étape 1 : Obtenir votre Clé Secrète DexpayAfrica

### 1.1 Connexion à votre Dashboard
1. **Accédez** à votre espace DexpayAfrica
   - URL : https://dashboard.dexpayafrica.com
   - Utilisez votre email et mot de passe

### 1.2 Accès aux Clés API
1. **Allez** dans **Settings** → **API Keys**
2. **Recherchez** votre clé existante ou créez-en une nouvelle
3. **Notez** votre clé secrète (commence par `sk_live_`)

### 1.3 Types de Clés
- **Clé publique** : `pk_live_XXXXX` (déjà configurée)
- **Clé secrète** : `sk_live_XXXXX` (à configurer maintenant)
- **Clé test** : `pk_test_XXXXX` (pour développement)

## 🔧 Étape 2 : Configuration dans le Code

### 2.1 Ouvrir le Fichier de Configuration
**Fichier** : `admin/api/dexpaya-payment.php`

### 2.2 Localisez les Lignes de Configuration
Trouvez ces lignes dans le fichier :

```php
// Configuration DexpayAfrica
define('DEXPAY_API_KEY', 'pk_live_d8598dde548b558c52ac0019b1eae791');
define('DEXPAY_SECRET_KEY', 'sk_live_'); // À configurer avec votre clé secrète
define('DEXPAY_WEBHOOK_SECRET', 'whsec_'); // À configurer pour les webhooks
```

### 2.3 Remplacez la Clé Secrète
```php
// Remplacez 'sk_live_' par votre vraie clé secrète
define('DEXPAY_SECRET_KEY', 'sk_live_VOTRE_VRAIE_CLE_SECRETE_ICI');
```

### 2.4 Configuration Webhook (Optionnel mais Recommandé)
```php
// Remplacez 'whsec_' par votre webhook secret
define('DEXPAY_WEBHOOK_SECRET', 'whsec_VOTRE_WEBHOOK_SECRET');
```

### 2.5 Mode Production
```php
// Pour passer en mode production
define('DEXPAY_DEBUG', false);
```

## 🌐 Étape 3 : Configuration Webhook

### 3.1 Accès Webhook dans DexpayAfrica
1. **Allez** dans **Settings** → **Webhooks**
2. **Créez** un nouveau webhook
3. **URL du webhook** : `https://votredomaine.com/admin/api/dexpaya-payment.php?action=webhook`
4. **Secret** : Utilisez la valeur de `DEXPAY_WEBHOOK_SECRET`
5. **Événements** à écouter :
   - `transaction.completed`
   - `transaction.failed`
   - `transaction.cancelled`

### 3.2 Test du Webhook
1. **Cliquez** sur "Test Webhook"
2. **Envoyez** un test de paiement
3. **Vérifiez** que vous recevez la notification

## 🧪 Étape 4 : Test de Configuration

### 4.1 Test en Mode Développement
Le système utilise actuellement le mode développement avec simulation. Pour tester :

```php
// Le système est en mode debug
define('DEXPAY_DEBUG', true);
```

### 4.2 Test de l'API
1. **Accédez** à : `https://votredomaine.com/admin/api/dexpaya-payment.php?action=config`
2. **Vérifiez** la réponse JSON
3. **Devriez voir** :
```json
{
    "success": true,
    "data": {
        "api_key": "pk_live_d8598dde548b558c52ac0019b1eate791",
        "supported_methods": ["mobile_money", "card", "bank_transfer"],
        "supported_countries": ["SN", "CI", "ML", "BF", "NE", "TG", "BJ", "GN", "CM"]
    }
}
```

### 4.3 Test de Paiement
1. **Accédez** à `inscription.html`
2. **Remplissez** le formulaire
3. **Cliquez** sur "Payer avec DexpayAfrica"
4. **Sélectionnez** une méthode
5. **Testez** le flux complet

## 🚀 Étape 5 : Passage en Production

### 5.1 Désactiver le Mode Debug
```php
// Dans admin/api/dexpaya-payment.php
define('DEXPAY_DEBUG', false);
```

### 5.2 Vérification Fichier
Assurez-vous que votre fichier `admin/api/dexpaya-payment.php` contient :

```php
define('DEXPAY_SECRET_KEY', 'sk_live_VOTRE_VRAIE_CLE_SECRETE_ICI');
define('DEXPAY_WEBHOOK_SECRET', 'whsec_VOTRE_WEBHOOK_SECRET');
define('DEXPAY_DEBUG', false);
```

### 5.3 Redéploiement
1. **Téléchargez** le ZIP mis à jour
2. **Déployez** sur votre serveur
3. **Testez** le flux de paiement réel

## 🔍 Étape 6 : Vérification Finale

### 6.1 Test Complet du Flux
1. **Inscription** → Paiement DexpayAfrica
2. **Sélection** méthode → Formulaire adapté
3. **Paiement** → Confirmation automatique
4. **Webhook** → Mise à jour base de données
5. **Email** → Confirmation client

### 6.2 Logs et Monitoring
- **Logs PHP** : Vérifiez les erreurs dans vos logs serveur
- **Logs DexpayAfrica** : Consultez votre dashboard
- **Webhook** : Vérifiez les réponses des webhooks

## ⚠️ Dépannage Commun

### ❌ **"Clé API invalide"**
- **Cause** : Erreur dans la clé secrète
- **Solution** : Vérifiez la clé et réessayez

### ❌ **"Webhook non configuré"**
- **Cause** : Webhook non activé dans DexpayAfrica
- **Solution** : Configurez le webhook dans le dashboard DexpayAfrica

### ❌ **"Paiement échoué"**
- **Cause** : Informations invalides ou fonds insuffisants
- **Solution** : Vérifiez les informations et contactez le client

### ❌ **"Session expirée"**
- **Cause** : Timeout de 15 minutes
- **Solution** : Recréez une nouvelle session

## 📞 Support DexpayAfrica

### 📧 **Documentation Officielle**
- **Site** : https://docs.dexpayafrica.com
- **API** : https://api.dexpayafrica.com
- **Support** : support@dexpayafrica.com

### 📱 **Contact Commercial**
- **Email** : sales@dexpayafrica.com
- **Téléphone** : +221 33 864 00 00
- **WhatsApp** : Disponible sur leur site

### 🌍 **Réseaux Sociaux**
- **Facebook** : @DexpayAfrica
- **Twitter** : @DexpayAfrica
- **LinkedIn** : DexpayAfrica

## 🎉 Configuration Terminée !

Une fois votre clé secrète configurée, votre système Linekode sera prêt à accepter des paiements réels via DexpayAfrica !

### ✅ **Ce qui Fonctionne**
- ✅ **Paiements mobile money** (Orange Money, Wave, MTN, Moov)
- ✅ **Paiements par carte** (Visa, Mastercard)
- ✅ **Virements bancaires**
- ✅ **Notifications** automatiques
- ✅ **Statuts** en temps réel

### 🎯 **Prochaines Étapes**
1. **Testez** le flux complet
2. **Configurez** vos produits et tarifs
3. **Activez** les notifications
4. **Lancez** votre système !

**Votre système Linekode est maintenant équipé pour les paiements africains avec DexpayAfrica !** 🌍💳✨

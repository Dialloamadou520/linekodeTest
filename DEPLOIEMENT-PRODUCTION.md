# 🚀 Guide de Déploiement en Production

## ✅ Prérequis

Avant de commencer, assurez-vous d'avoir :
- ✅ Accès à votre serveur (FTP, SSH, ou cPanel)
- ✅ Nom de domaine configuré (ex: linekode.com)
- ✅ Certificat SSL/HTTPS activé
- ✅ PHP 7.4+ installé sur le serveur
- ✅ Extension cURL activée
- ✅ Clés API DexpayAfrica (Live)

---

## 📋 ÉTAPE 1 : Télécharger le Code depuis GitHub

### Option A : Via SSH (Recommandé)

```bash
# Se connecter au serveur
ssh votre_utilisateur@votre_serveur.com

# Aller dans le dossier web
cd /home/votre_utilisateur/public_html
# OU
cd /var/www/html

# Cloner le dépôt
git clone https://github.com/Dialloamadou520/linekodeTest.git linekode

# Aller dans le dossier
cd linekode
```

### Option B : Via FTP/cPanel File Manager

1. Téléchargez le code depuis GitHub :
   - Allez sur https://github.com/Dialloamadou520/linekodeTest
   - Cliquez sur **Code** → **Download ZIP**
   - Extrayez le fichier ZIP

2. Uploadez via FTP ou File Manager :
   - Connectez-vous à votre cPanel
   - Allez dans **File Manager**
   - Naviguez vers `public_html`
   - Uploadez tous les fichiers

---

## 🔑 ÉTAPE 2 : Configurer les Clés API

### Créer le fichier `.env`

**Via SSH :**
```bash
cd /home/votre_utilisateur/public_html/linekode
cp .env.example .env
nano .env
```

**Via cPanel File Manager :**
1. Copiez `.env.example` → `.env`
2. Cliquez droit sur `.env` → **Edit**

### Remplir le fichier `.env`

```env
# Configuration DexpayAfrica
DEXPAY_API_KEY=pk_live_0a143d68bb99cf63c97dc1cc6779cc95
DEXPAY_SECRET_KEY=sk_live_VOTRE_CLE_SECRETE_ICI
DEXPAY_BASE_URL=https://api.dexpay.africa/api/v1
DEXPAY_SANDBOX_MODE=false

# URLs de votre site (IMPORTANT : Remplacez par votre domaine)
SITE_URL=https://linekode.com
SUCCESS_URL=https://linekode.com/payment-success.php
FAILURE_URL=https://linekode.com/payment-cancelled.php
WEBHOOK_URL=https://linekode.com/api/dexpay-webhook.php

# Configuration du site
SITE_NAME=Linekode
SITE_EMAIL=linekodesn@gmail.com
ADMIN_EMAIL=linekodesn@gmail.com

# Montant et devise
INSCRIPTION_AMOUNT=50000
COUNTRY_ISO=SN
CURRENCY=XOF
```

**⚠️ IMPORTANT :** Remplacez `VOTRE_CLE_SECRETE_ICI` par votre vraie clé secrète DexpayAfrica.

---

## 📝 ÉTAPE 3 : Créer le fichier `config.php`

```bash
cp config.example.php config.php
```

Le fichier `config.php` chargera automatiquement les variables depuis `.env`.

---

## 📁 ÉTAPE 4 : Créer les Dossiers Nécessaires

```bash
# Créer le dossier data pour stocker les paiements
mkdir -p data
chmod 755 data

# Créer le dossier logs (optionnel)
mkdir -p logs
chmod 755 logs
```

**Via cPanel :**
1. File Manager → Créer un nouveau dossier `data`
2. Permissions : 755

---

## 🔒 ÉTAPE 5 : Sécuriser les Fichiers Sensibles

### Créer un fichier `.htaccess` dans le dossier racine

```bash
nano .htaccess
```

Ajoutez :
```apache
# Protéger les fichiers sensibles
<FilesMatch "^\.env$">
    Order allow,deny
    Deny from all
</FilesMatch>

<FilesMatch "^config\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Activer HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 🌐 ÉTAPE 6 : Configurer le Webhook DexpayAfrica

### 1. Aller sur le Portail DexpayAfrica

https://portal.dexpay.africa/webhooks

### 2. Ajouter un Webhook

- **URL** : `https://linekode.com/api/dexpay-webhook.php`
- **Événements à activer** :
  - ✅ `checkout.completed`
  - ✅ `payment.success`
  - ✅ `payment.failed`
  - ✅ `checkout.cancelled`

### 3. Sauvegarder

Cliquez sur **Save** ou **Créer**.

---

## ✅ ÉTAPE 7 : Vérifier l'Installation

### Test 1 : Vérifier que le site est accessible

Ouvrez votre navigateur et allez sur :
- `https://linekode.com` → Page d'accueil
- `https://linekode.com/inscription.php` → Formulaire d'inscription

### Test 2 : Tester l'API DexpayAfrica

Créez un fichier de test temporaire :

```bash
nano test-production.php
```

Contenu :
```php
<?php
// Charger la configuration
require_once 'config.php';

echo "=== TEST DE CONFIGURATION ===\n\n";
echo "API Key: " . (defined('DEXPAY_API_KEY') ? '✅ Configurée' : '❌ Manquante') . "\n";
echo "Secret Key: " . (defined('DEXPAY_SECRET_KEY') ? '✅ Configurée' : '❌ Manquante') . "\n";
echo "Base URL: " . DEXPAY_BASE_URL . "\n";
echo "Site URL: " . SITE_URL . "\n";
echo "Webhook URL: " . WEBHOOK_URL . "\n\n";

// Test cURL
if (function_exists('curl_init')) {
    echo "✅ cURL est activé\n";
} else {
    echo "❌ cURL n'est PAS activé - Contactez votre hébergeur\n";
}

// Test permissions dossier data
if (is_writable(__DIR__ . '/data')) {
    echo "✅ Dossier data accessible en écriture\n";
} else {
    echo "❌ Dossier data non accessible - Vérifiez les permissions\n";
}

echo "\n=== FIN DU TEST ===\n";
```

Accédez à : `https://linekode.com/test-production.php`

**⚠️ IMPORTANT :** Supprimez ce fichier après le test !

```bash
rm test-production.php
```

---

## 🧪 ÉTAPE 8 : Test de Paiement Réel

### 1. Aller sur le formulaire d'inscription

`https://linekode.com/inscription.php`

### 2. Remplir le formulaire

- Nom, prénom, email, téléphone
- Choisir une formation
- Montant : 50 000 FCFA

### 3. Cliquer sur "Payer avec DexpayAfrica"

Vous serez redirigé vers DexpayAfrica.

### 4. Effectuer un paiement test

- Choisissez Wave, Orange Money, ou Carte
- Suivez les instructions

### 5. Vérifier la redirection

- ✅ Succès → `payment-success.php`
- ❌ Échec → `payment-cancelled.php`

### 6. Vérifier le webhook

Consultez le fichier `data/payments.json` :

```bash
cat data/payments.json
```

Vous devriez voir les données du paiement.

---

## 📊 ÉTAPE 9 : Monitoring et Logs

### Consulter les logs PHP

```bash
# Sur cPanel
tail -f /home/votre_utilisateur/logs/error_log

# Sur serveur Linux
tail -f /var/log/apache2/error.log
```

### Consulter les paiements

```bash
cat data/payments.json
```

---

## 🔧 Dépannage

### Problème : "cURL error" ou "Connection timeout"

**Solution :**
1. Vérifiez que cURL est activé sur votre serveur
2. Contactez votre hébergeur pour activer cURL
3. Vérifiez que votre serveur peut faire des requêtes HTTPS sortantes

### Problème : "Webhook non reçu"

**Solution :**
1. Vérifiez l'URL du webhook sur https://portal.dexpay.africa/webhooks
2. Assurez-vous que `api/dexpay-webhook.php` est accessible publiquement
3. Testez : `curl https://linekode.com/api/dexpay-webhook.php`

### Problème : "Permission denied" sur le dossier data

**Solution :**
```bash
chmod 755 data
chown votre_utilisateur:votre_utilisateur data
```

### Problème : Les variables d'environnement ne sont pas chargées

**Solution :**
Vérifiez que `config.php` existe et charge bien le fichier `.env`.

---

## 🔐 Sécurité Post-Déploiement

### 1. Vérifier les permissions

```bash
# Fichiers : 644
find . -type f -exec chmod 644 {} \;

# Dossiers : 755
find . -type d -exec chmod 755 {} \;

# Fichiers sensibles : 600
chmod 600 .env
chmod 600 config.php
```

### 2. Désactiver l'affichage des erreurs en production

Dans `config.php`, vérifiez :
```php
define('DEBUG_MODE', false);
```

### 3. Sauvegardes régulières

Configurez des sauvegardes automatiques :
- Base de données (si vous en utilisez une)
- Fichier `data/payments.json`
- Fichier `.env`

---

## ✅ Checklist Finale

- [ ] Code uploadé sur le serveur
- [ ] Fichier `.env` créé avec vraies clés API
- [ ] Fichier `config.php` créé
- [ ] Dossier `data/` créé avec bonnes permissions
- [ ] Webhook configuré sur DexpayAfrica
- [ ] HTTPS activé et fonctionnel
- [ ] Test de paiement réel effectué avec succès
- [ ] Webhook reçu et données enregistrées
- [ ] Pages de succès/échec fonctionnelles
- [ ] Fichiers de test supprimés
- [ ] Permissions sécurisées
- [ ] Mode debug désactivé

---

## 🎉 Félicitations !

Votre site Linekode est maintenant **EN LIGNE** et **OPÉRATIONNEL** avec le système de paiement DexpayAfrica !

### Liens Importants

- **Site** : https://linekode.com
- **Inscription** : https://linekode.com/inscription.php
- **Portail DexpayAfrica** : https://portal.dexpay.africa
- **Documentation API** : https://docs.dexpay.africa

### Support

- **Email** : linekodesn@gmail.com
- **Téléphone** : +221 71 117 93 93
- **Support DexpayAfrica** : support@dexpay.africa

---

**Version** : 1.0 Production  
**Date** : Mars 2026  
**Statut** : ✅ PRÊT POUR LA PRODUCTION

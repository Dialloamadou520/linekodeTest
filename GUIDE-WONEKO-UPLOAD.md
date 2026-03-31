# 📦 Guide d'Upload sur Woneko

## ✅ Fichier ZIP Créé

Le fichier **`linekode-production.zip`** a été créé dans votre dossier projet.

**Emplacement :** `c:\xampp\htdocs\linekode-PAIEMENT-DIRECT\linekode-production.zip`

---

## 🚀 Étapes d'Upload sur Woneko

### 1. Se Connecter à Woneko

1. Allez sur **https://woneko.com** (ou votre panel Woneko)
2. Connectez-vous avec vos identifiants
3. Accédez au **File Manager** ou **Gestionnaire de Fichiers**

### 2. Uploader le Fichier ZIP

1. Dans le File Manager, allez dans le dossier **`public_html`** ou **`www`**
2. Cliquez sur **Upload** ou **Téléverser**
3. Sélectionnez le fichier **`linekode-production.zip`**
4. Attendez la fin de l'upload (peut prendre quelques minutes selon votre connexion)

### 3. Extraire le ZIP

1. Une fois uploadé, faites un **clic droit** sur `linekode-production.zip`
2. Sélectionnez **Extract** ou **Extraire**
3. Confirmez l'extraction
4. Tous les fichiers seront extraits dans `public_html`

### 4. Supprimer le ZIP (Optionnel)

Après extraction, vous pouvez supprimer le fichier ZIP pour économiser de l'espace :
- Clic droit sur `linekode-production.zip` → **Delete**

---

## 🔑 Configuration Post-Upload

### ÉTAPE 1 : Créer le fichier `.env`

1. Dans le File Manager, trouvez le fichier **`.env.example`**
2. **Copiez** `.env.example` → `.env`
3. **Éditez** le fichier `.env` (clic droit → Edit)
4. Remplissez avec vos vraies clés :

```env
# Configuration DexpayAfrica
DEXPAY_API_KEY=pk_live_0a143d68bb99cf63c97dc1cc6779cc95
DEXPAY_SECRET_KEY=sk_live_VOTRE_CLE_SECRETE_ICI

# URLs de votre site (IMPORTANT : Remplacez par votre domaine)
SITE_URL=https://linekode.com
SUCCESS_URL=https://linekode.com/payment-success.php
FAILURE_URL=https://linekode.com/payment-cancelled.php
WEBHOOK_URL=https://linekode.com/api/dexpay-webhook.php

# Configuration
SITE_NAME=Linekode
SITE_EMAIL=linekodesn@gmail.com
ADMIN_EMAIL=linekodesn@gmail.com
INSCRIPTION_AMOUNT=50000
COUNTRY_ISO=SN
CURRENCY=XOF
```

5. **Sauvegardez** le fichier

### ÉTAPE 2 : Créer le fichier `config.php`

1. Copiez **`config.example.php`** → **`config.php`**
2. Pas besoin de l'éditer, il chargera automatiquement `.env`

### ÉTAPE 3 : Créer le Dossier `data`

1. Dans `public_html`, créez un nouveau dossier nommé **`data`**
2. Permissions : **755** (généralement par défaut)

---

## 🌐 Configuration du Webhook DexpayAfrica

### 1. Aller sur le Portail DexpayAfrica

https://portal.dexpay.africa/webhooks

### 2. Ajouter un Webhook

- **URL du Webhook** : `https://linekode.com/api/dexpay-webhook.php`
  (Remplacez `linekode.com` par votre vrai domaine)

### 3. Activer les Événements

Cochez tous les événements de paiement :
- ✅ `checkout.completed`
- ✅ `payment.success`
- ✅ `payment.failed`
- ✅ `checkout.cancelled`

### 4. Sauvegarder

Cliquez sur **Save** ou **Créer**

---

## ✅ Vérification de l'Installation

### 1. Tester l'Accès au Site

Ouvrez votre navigateur et allez sur :
- **Page d'accueil** : `https://linekode.com`
- **Inscription** : `https://linekode.com/inscription.php`

### 2. Vérifier la Configuration

Accédez à : `https://linekode.com/verifier-installation.php`

Ce script vérifiera :
- ✅ Version PHP
- ✅ Extension cURL
- ✅ Fichiers `.env` et `config.php`
- ✅ Dossier `data`
- ✅ Connexion API DexpayAfrica

**⚠️ IMPORTANT :** Supprimez ce fichier après vérification !

### 3. Test de Paiement

1. Allez sur `https://linekode.com/inscription.php`
2. Remplissez le formulaire
3. Cliquez sur **"Payer avec DexpayAfrica"**
4. Effectuez un paiement test
5. Vérifiez la redirection vers la page de succès

---

## 🔒 Sécurité

### Protéger les Fichiers Sensibles

Créez un fichier **`.htaccess`** dans `public_html` :

```apache
# Protéger .env
<FilesMatch "^\.env$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protéger config.php
<FilesMatch "^config\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Forcer HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 📋 Checklist Finale

- [ ] Fichier ZIP uploadé sur Woneko
- [ ] ZIP extrait dans `public_html`
- [ ] Fichier `.env` créé avec vraies clés API
- [ ] Fichier `config.php` créé
- [ ] Dossier `data` créé
- [ ] Webhook configuré sur DexpayAfrica
- [ ] Test avec `verifier-installation.php` réussi
- [ ] Fichier `verifier-installation.php` supprimé
- [ ] Test de paiement réel effectué
- [ ] Site accessible via HTTPS

---

## 🆘 Problèmes Courants

### Le site affiche une erreur 500

**Solution :**
- Vérifiez les permissions des fichiers (644) et dossiers (755)
- Vérifiez que le fichier `.env` existe
- Consultez les logs d'erreur dans le panel Woneko

### Le webhook ne fonctionne pas

**Solution :**
- Vérifiez l'URL sur https://portal.dexpay.africa/webhooks
- Testez l'accès : `curl https://linekode.com/api/dexpay-webhook.php`
- Vérifiez les permissions du dossier `data`

### Les paiements ne sont pas enregistrés

**Solution :**
- Vérifiez que le dossier `data` existe et est accessible en écriture
- Vérifiez les logs dans le panel Woneko

---

## 📞 Support

**Woneko :** support@woneko.com  
**DexpayAfrica :** support@dexpay.africa  
**Linekode :** linekodesn@gmail.com / +221 71 117 93 93

---

## 🎉 Félicitations !

Votre site Linekode est maintenant **EN LIGNE** sur Woneko avec le système de paiement DexpayAfrica !

**URL du site :** https://linekode.com  
**Formulaire d'inscription :** https://linekode.com/inscription.php

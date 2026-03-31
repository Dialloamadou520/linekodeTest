# 🚀 Instructions Rapides - Mise en Ligne

## ⚡ Version Courte (5 minutes)

### 1. Uploader les fichiers sur votre serveur
- Via FTP ou cPanel File Manager
- Dossier : `public_html` ou `www`

### 2. Créer le fichier `.env`
```bash
# Copiez .env.example vers .env
# Éditez .env et ajoutez vos clés :
DEXPAY_API_KEY=pk_live_0a143d68bb99cf63c97dc1cc6779cc95
DEXPAY_SECRET_KEY=sk_live_VOTRE_CLE_SECRETE
SITE_URL=https://linekode.com
```

### 3. Créer le fichier `config.php`
```bash
# Copiez config.example.php vers config.php
cp config.example.php config.php
```

### 4. Créer le dossier `data`
```bash
mkdir data
chmod 755 data
```

### 5. Configurer le Webhook
- Allez sur https://portal.dexpay.africa/webhooks
- URL : `https://linekode.com/api/dexpay-webhook.php`
- Activez tous les événements de paiement

### 6. Tester
- Allez sur `https://linekode.com/inscription.php`
- Faites un paiement test

---

## 🔑 Vos Clés API DexpayAfrica

**Clé Publique :**
```
pk_live_0a143d68bb99cf63c97dc1cc6779cc95
```

**Clé Secrète :**
```
À récupérer sur https://portal.dexpay.africa/api-keys
```

---

## 📞 En Cas de Problème

1. **cURL non activé** → Contactez votre hébergeur
2. **Webhook non reçu** → Vérifiez l'URL sur le portail DexpayAfrica
3. **Erreur 500** → Vérifiez les permissions du dossier `data`

---

## ✅ C'est Tout !

Votre site sera opérationnel en quelques minutes.

Pour plus de détails, consultez `DEPLOIEMENT-PRODUCTION.md`

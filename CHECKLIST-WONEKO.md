# ✅ Checklist de Déploiement Woneko

## 📋 À Faire Après l'Upload

### ☐ 1. Upload et Extraction
- [ ] Uploader `linekode-production.zip` sur Woneko
- [ ] Aller dans File Manager → `public_html`
- [ ] Extraire le ZIP (clic droit → Extract)
- [ ] Supprimer le fichier ZIP (optionnel)

### ☐ 2. Configuration `.env` (CRITIQUE)
- [ ] Copier `.env.example` → `.env`
- [ ] Éditer `.env` et remplir :
  ```
  DEXPAY_API_KEY=pk_live_0a143d68bb99cf63c97dc1cc6779cc95
  DEXPAY_SECRET_KEY=sk_live_VOTRE_CLE_SECRETE
  SITE_URL=https://linekode.com
  WEBHOOK_URL=https://linekode.com/api/dexpay-webhook.php
  ```
- [ ] Sauvegarder le fichier

### ☐ 3. Configuration `config.php`
- [ ] Copier `config.example.php` → `config.php`
- [ ] Pas besoin de l'éditer

### ☐ 4. Créer le Dossier `data`
- [ ] Créer un nouveau dossier nommé `data`
- [ ] Permissions : 755

### ☐ 5. Webhook DexpayAfrica
- [ ] Aller sur https://portal.dexpay.africa/webhooks
- [ ] Ajouter webhook : `https://linekode.com/api/dexpay-webhook.php`
- [ ] Activer tous les événements
- [ ] Sauvegarder

### ☐ 6. Vérification
- [ ] Accéder à `https://linekode.com/verifier-installation.php`
- [ ] Vérifier que tout est ✅ vert
- [ ] Supprimer `verifier-installation.php` après

### ☐ 7. Test de Paiement
- [ ] Aller sur `https://linekode.com/inscription.php`
- [ ] Remplir le formulaire
- [ ] Effectuer un paiement test
- [ ] Vérifier la redirection de succès

---

## 🔑 Clés à Utiliser

**API Key (Publique) :**
```
pk_live_0a143d68bb99cf63c97dc1cc6779cc95
```

**Secret Key :**
À récupérer sur https://portal.dexpay.africa/api-keys

---

## 📞 Besoin d'Aide ?

Si vous rencontrez un problème, dites-moi à quelle étape vous êtes bloqué !

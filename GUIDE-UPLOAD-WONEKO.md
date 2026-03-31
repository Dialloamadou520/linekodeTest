# 🚀 Guide d'Upload sur Woneko - Mode Simulation

## 📦 Fichier à Uploader

**`linekode-woneko-simulation.zip`**

Emplacement : `C:\xampp\htdocs\linekode-woneko-simulation.zip`

## 📋 Étapes Détaillées

### **Étape 1 : Préparer le Fichier**

1. ✅ Localisez le fichier `linekode-woneko-simulation.zip` sur votre PC
2. ✅ Vérifiez la taille : environ 2.4 MB
3. ✅ Le fichier contient le mode simulation activé

### **Étape 2 : Connexion à Woneko**

1. **Ouvrez** votre navigateur
2. **Accédez** au panneau de contrôle Woneko
3. **Connectez-vous** avec vos identifiants
4. **Cherchez** "Gestionnaire de fichiers" ou "File Manager"

### **Étape 3 : Nettoyer l'Ancien Site**

⚠️ **Important** : Supprimez d'abord les anciens fichiers

1. **Naviguez** vers `public_html/` (ou `www/`)
2. **Sélectionnez TOUS** les fichiers et dossiers actuels :
   - index.html
   - about.html
   - contact.html
   - formations.html
   - inscription.html
   - payment-success.html
   - payment-cancelled.html
   - Dossiers : css/, js/, images/, api/
   - Fichiers .md (documentation)

3. **Supprimez** tous ces fichiers
4. **Vérifiez** que `public_html/` est vide

### **Étape 4 : Upload du Nouveau ZIP**

1. **Cliquez** sur "Upload" ou "Télécharger"
2. **Sélectionnez** `linekode-woneko-simulation.zip`
3. **Attendez** la fin de l'upload (quelques secondes)
4. **Vérifiez** que le ZIP apparaît dans `public_html/`

### **Étape 5 : Décompresser le ZIP**

1. **Clic droit** sur `linekode-woneko-simulation.zip`
2. **Sélectionnez** "Extract" ou "Extraire"
3. **Choisissez** `public_html/` comme destination
4. **Attendez** la décompression

### **Étape 6 : Vérifier la Structure**

Après décompression, vous devriez voir :

```
public_html/
├── index.html
├── about.html
├── contact.html
├── formations.html
├── inscription.html
├── payment-success.html
├── payment-cancelled.html
├── css/
├── js/
├── images/
├── api/
│   ├── dexpay-checkout.php (MODE SIMULATION = true)
│   └── test-dexpay-connection.php
├── CONFIGURATION.md
├── PRODUCTION-READY.md
├── TEST-CONNEXION-DEXPAY.md
├── DEPLOIEMENT-WONEKO.md
└── PROBLEME-WONEKO-DNS.md
```

### **Étape 7 : Supprimer le ZIP**

1. **Sélectionnez** `linekode-woneko-simulation.zip`
2. **Supprimez-le** (optionnel, pour économiser l'espace)

### **Étape 8 : Vérifier les Permissions**

1. **Dossier `api/`** → Permissions `755`
2. **Fichiers `.php`** → Permissions `644`
3. **Fichiers `.html`** → Permissions `644`

## ✅ Test Après Upload

### **Test 1 : Site Accessible**

Ouvrez : `https://linekode.com`

**Résultat attendu** : Page d'accueil s'affiche ✅

### **Test 2 : Page d'Inscription**

Ouvrez : `https://linekode.com/inscription.html`

**Résultat attendu** : Formulaire d'inscription s'affiche ✅

### **Test 3 : Inscription Complète**

1. **Remplissez** le formulaire d'inscription
2. **Validez** le formulaire
3. **Observez** : Message "Redirection vers le paiement..."

**Résultat attendu** : 
- ✅ Redirection immédiate (1-2 secondes max)
- ✅ Page `payment-success.html` s'affiche
- ✅ PAS de chargement infini

### **Test 4 : Vérifier le Mode Simulation**

Ouvrez : `https://linekode.com/api/test-dexpay-connection.php`

**Résultat attendu** :
```json
{
  "summary": {
    "status": "failed",
    "message": "⚠️ Problèmes de connexion détectés"
  }
}
```

C'est normal ! Le mode simulation contourne ce problème.

## 🎯 Résultat Final

Après l'upload, votre site :

- ✅ **Fonctionne normalement**
- ✅ **Pas de chargement infini**
- ✅ **Inscriptions enregistrées**
- ✅ **Redirection automatique**
- ⚠️ **Paiements simulés** (pas de vrais paiements)

## ⚠️ Important

### **Mode Simulation Actif**

Le fichier `api/dexpay-checkout.php` contient :
```php
define('SIMULATION_MODE', true);
```

Cela signifie :
- ✅ Le site fonctionne
- ⚠️ Les paiements ne sont PAS réels
- ⚠️ DexpayAfrica n'est PAS contacté
- ✅ Les utilisateurs peuvent s'inscrire

### **Pour Passer en Production**

Quand Woneko aura autorisé `api.dexpayafrica.com` :

1. Modifiez `api/dexpay-checkout.php` ligne 20
2. Changez `true` en `false`
3. Testez avec `test-dexpay-connection.php`
4. Vérifiez que le statut est `"success"`

## 🐛 Dépannage

### **Problème : Page blanche**

**Solution** :
- Vérifiez que tous les fichiers sont décompressés
- Vérifiez les permissions (755/644)
- Consultez les logs d'erreur PHP

### **Problème : Erreur 404**

**Solution** :
- Vérifiez que `index.html` est dans `public_html/`
- Vérifiez que le nom de domaine pointe vers Woneko

### **Problème : Chargement infini persiste**

**Solution** :
- Vérifiez que le bon ZIP a été uploadé
- Ouvrez `api/dexpay-checkout.php` et vérifiez ligne 20
- Doit être : `define('SIMULATION_MODE', true);`

### **Problème : CSS ne s'affiche pas**

**Solution** :
- Vérifiez que le dossier `css/` existe
- Vérifiez les permissions du dossier `css/` (755)
- Videz le cache du navigateur (Ctrl+F5)

## 📞 Support

### **Si le problème persiste après upload**

1. Vérifiez les logs d'erreur sur Woneko
2. Testez en navigation privée
3. Videz le cache du navigateur
4. Contactez-moi avec une capture d'écran

### **Woneko Support**

Continuez à demander l'autorisation pour `api.dexpayafrica.com` afin de passer en mode production.

## ✅ Checklist Finale

- [ ] Ancien site supprimé de `public_html/`
- [ ] `linekode-woneko-simulation.zip` uploadé
- [ ] ZIP décompressé dans `public_html/`
- [ ] Structure des dossiers vérifiée
- [ ] Permissions configurées (755/644)
- [ ] Site accessible sur https://linekode.com
- [ ] Formulaire d'inscription testé
- [ ] Redirection fonctionne (pas de chargement infini)
- [ ] Page de succès s'affiche

**Votre site fonctionnera immédiatement après ces étapes !** 🎉

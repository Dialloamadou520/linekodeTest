# Guide de Déploiement sur GitHub

## ✅ Étapes Complétées

- ✅ Dépôt Git initialisé
- ✅ Fichiers de configuration créés (.gitignore, .env.example, config.example.php)
- ✅ README.md créé avec documentation complète
- ✅ Premier commit effectué (112 fichiers, 27751 lignes)

---

## 🚀 Prochaines Étapes

### 1. Créer un Dépôt sur GitHub

1. Allez sur https://github.com
2. Cliquez sur le bouton **"New"** (ou "+" en haut à droite → "New repository")
3. Remplissez les informations :
   - **Repository name** : `linekode-paiement-direct` (ou le nom de votre choix)
   - **Description** : "Site d'inscription Linekode avec intégration DexpayAfrica"
   - **Visibility** : Choisissez **Private** (recommandé) ou Public
   - **NE PAS** cocher "Initialize this repository with a README" (vous en avez déjà un)
4. Cliquez sur **"Create repository"**

### 2. Connecter votre Dépôt Local à GitHub

GitHub vous donnera des commandes. Utilisez celles pour un dépôt existant :

```bash
cd c:\xampp\htdocs\linekode-PAIEMENT-DIRECT

# Ajouter le dépôt distant (remplacez VOTRE_USERNAME par votre nom d'utilisateur GitHub)
git remote add origin https://github.com/VOTRE_USERNAME/linekode-paiement-direct.git

# Renommer la branche principale en 'main' (optionnel mais recommandé)
git branch -M main

# Pousser le code vers GitHub
git push -u origin main
```

### 3. Vérifier le Déploiement

1. Rafraîchissez la page de votre dépôt GitHub
2. Vous devriez voir tous vos fichiers
3. Le README.md s'affichera automatiquement sur la page d'accueil

---

## 🔒 Sécurité - IMPORTANT

### Fichiers Exclus de Git (via .gitignore)

Les fichiers suivants **NE SERONT PAS** envoyés sur GitHub :
- ✅ `.env` (vos clés API)
- ✅ `config.php` (configuration avec clés)
- ✅ `data/` (données de paiement)
- ✅ `logs/` (logs du serveur)

### Fichiers Inclus (exemples sans clés réelles)

Les fichiers suivants **SERONT** envoyés sur GitHub :
- ✅ `.env.example` (exemple sans vraies clés)
- ✅ `config.example.php` (exemple de configuration)
- ✅ `README.md` (documentation)
- ✅ Tous les fichiers du site (HTML, CSS, JS, PHP)

---

## ⚠️ Avant de Pousser sur GitHub

### Vérification de Sécurité

Assurez-vous qu'aucune clé API réelle n'est dans le code :

```bash
# Rechercher les clés API dans les fichiers suivis par Git
git grep "pk_live_" -- ':!.env' ':!config.php'
git grep "sk_live_" -- ':!.env' ':!config.php'
```

Si des clés apparaissent dans d'autres fichiers, vous devez les remplacer par des variables d'environnement.

---

## 📝 Commandes Git Utiles

### Voir le statut
```bash
git status
```

### Voir l'historique des commits
```bash
git log --oneline
```

### Ajouter des modifications
```bash
git add .
git commit -m "Description de vos modifications"
git push
```

### Créer une branche pour développement
```bash
git checkout -b developpement
git push -u origin developpement
```

---

## 🌐 Déploiement en Production

Une fois sur GitHub, vous pouvez déployer sur votre serveur de plusieurs façons :

### Option 1 : Clone Direct sur le Serveur

```bash
# Sur votre serveur (via SSH)
cd /var/www/html  # ou votre dossier web
git clone https://github.com/VOTRE_USERNAME/linekode-paiement-direct.git
cd linekode-paiement-direct

# Créer le fichier .env avec vos vraies clés
cp .env.example .env
nano .env  # Éditez et ajoutez vos clés

# Créer le fichier config.php
cp config.example.php config.php

# Créer le dossier data
mkdir data
chmod 755 data
```

### Option 2 : Upload FTP/cPanel

1. Téléchargez le code depuis GitHub (bouton "Code" → "Download ZIP")
2. Uploadez via FTP ou File Manager de cPanel
3. Créez `.env` et `config.php` avec vos clés
4. Configurez les permissions

### Option 3 : Déploiement Automatique (GitHub Actions)

Vous pouvez configurer un déploiement automatique à chaque push. Demandez-moi si vous voulez cette option.

---

## 🔄 Workflow Recommandé

### Développement Local
1. Faites vos modifications localement
2. Testez sur `localhost`
3. Commitez : `git add . && git commit -m "Description"`
4. Poussez : `git push`

### Mise en Production
1. Connectez-vous à votre serveur
2. Allez dans le dossier du site : `cd /var/www/html/linekode-paiement-direct`
3. Récupérez les dernières modifications : `git pull`
4. Testez le site

---

## 📋 Checklist Avant Mise en Ligne

- [ ] Code poussé sur GitHub
- [ ] Fichier `.env` créé sur le serveur avec vraies clés
- [ ] Fichier `config.php` créé sur le serveur
- [ ] Dossier `data/` créé avec bonnes permissions
- [ ] Webhook configuré sur DexpayAfrica
- [ ] HTTPS activé sur le domaine
- [ ] Test de paiement réel effectué
- [ ] Pages de succès/échec vérifiées

---

## 🆘 Dépannage

### Erreur : "remote origin already exists"
```bash
git remote remove origin
git remote add origin https://github.com/VOTRE_USERNAME/linekode-paiement-direct.git
```

### Erreur : "Permission denied"
Vous devez vous authentifier avec GitHub :
- Utilisez un Personal Access Token (recommandé)
- Ou configurez SSH

### Créer un Personal Access Token
1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token
3. Cochez "repo"
4. Utilisez ce token comme mot de passe lors du push

---

## 📞 Support

Si vous avez des questions sur le déploiement, consultez :
- Documentation Git : https://git-scm.com/doc
- Documentation GitHub : https://docs.github.com

---

**Prêt pour GitHub !** 🚀

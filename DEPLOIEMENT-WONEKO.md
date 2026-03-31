# 🚀 Guide de Déploiement Woneko - Linekode

## 📋 Préparation au Déploiement

Votre site Linekode est prêt à être déployé sur **Woneko** avec paiements DexpayAfrica.

## 📦 Fichier à Déployer

**`linekode-projet-complet.zip`** - Version complète avec paiement automatique

## 🌐 Déploiement sur Woneko

### Étape 1 : Connexion à Woneko

1. **Accédez** à votre compte Woneko
2. **Connectez-vous** avec vos identifiants
3. **Accédez** au gestionnaire de fichiers ou FTP

### Étape 2 : Upload des Fichiers

#### Option A : Via le Gestionnaire de Fichiers Woneko

1. **Accédez** au gestionnaire de fichiers
2. **Naviguez** vers le dossier racine (généralement `public_html/` ou `www/`)
3. **Uploadez** le fichier `linekode-projet-complet.zip`
4. **Décompressez** le ZIP directement sur le serveur
5. **Déplacez** tous les fichiers vers la racine si nécessaire

#### Option B : Via FTP (FileZilla)

1. **Ouvrez** FileZilla ou votre client FTP
2. **Connectez-vous** avec vos identifiants Woneko :
   - Hôte : `ftp.votredomaine.com` ou IP fournie par Woneko
   - Utilisateur : Votre nom d'utilisateur Woneko
   - Mot de passe : Votre mot de passe Woneko
   - Port : 21 (FTP) ou 22 (SFTP)

3. **Naviguez** vers `public_html/` ou `www/`
4. **Uploadez** tous les fichiers du ZIP décompressé

### Étape 3 : Structure des Fichiers sur Woneko

Assurez-vous que la structure est :

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
│   ├── style.css
│   ├── payment-styles.css
│   └── whatsapp-styles.css
├── images/
├── js/
│   ├── script.js
│   └── payment-handler.js
├── api/
│   ├── dexpay-checkout.php
│   └── test-dexpay-connection.php
├── CONFIGURATION.md
├── PRODUCTION-READY.md
└── TEST-CONNEXION-DEXPAY.md
```

## 🔧 Configuration PHP sur Woneko

### Vérifier les Extensions PHP

Woneko doit avoir ces extensions activées :
- ✅ **cURL** - Pour appels API DexpayAfrica
- ✅ **JSON** - Pour traitement des données
- ✅ **OpenSSL** - Pour connexions HTTPS

### Activer les Extensions (si nécessaire)

1. **Accédez** au panneau de contrôle Woneko
2. **Cherchez** "PHP Settings" ou "Configuration PHP"
3. **Activez** les extensions requises
4. **Redémarrez** le service si demandé

### Version PHP Recommandée

- **Minimum** : PHP 7.4
- **Recommandé** : PHP 8.0 ou supérieur

## 🔐 Permissions des Fichiers

### Définir les Permissions Correctes

```bash
# Via SSH (si disponible)
chmod 755 api/
chmod 644 api/*.php
chmod 644 *.html
chmod 755 css/ js/ images/
```

### Via FTP
1. **Clic droit** sur le dossier `api/`
2. **Permissions** → `755`
3. **Clic droit** sur les fichiers `.php`
4. **Permissions** → `644`

## 🌐 Configuration du Domaine

### Si vous utilisez linekode.com

1. **Pointez** le domaine vers les serveurs Woneko
2. **Attendez** la propagation DNS (24-48h max)
3. **Vérifiez** que le site est accessible

### Configuration DNS

Assurez-vous que les enregistrements DNS pointent vers Woneko :
```
Type A : linekode.com → IP de Woneko
Type A : www.linekode.com → IP de Woneko
```

## 🔒 Activer HTTPS (SSL)

### Important pour DexpayAfrica

DexpayAfrica **requiert HTTPS** en production.

### Sur Woneko

1. **Accédez** au panneau SSL/TLS
2. **Activez** Let's Encrypt (gratuit) ou uploadez votre certificat
3. **Forcez** la redirection HTTP → HTTPS
4. **Vérifiez** que https://linekode.com fonctionne

## 🧪 Tests Après Déploiement

### 1. Test de Base

Vérifiez que le site est accessible :
```
https://linekode.com
```

### 2. Test de Connexion DexpayAfrica

Accédez au script de test :
```
https://linekode.com/api/test-dexpay-connection.php
```

**Résultat attendu** :
```json
{
  "summary": {
    "status": "success",
    "message": "✅ Connexion DexpayAfrica fonctionnelle !"
  }
}
```

### 3. Test du Formulaire d'Inscription

1. **Allez** sur https://linekode.com/inscription.html
2. **Remplissez** le formulaire
3. **Validez** l'inscription
4. **Vérifiez** la redirection vers DexpayAfrica

### 4. Test de Paiement Réel

⚠️ **Important** : Testez avec un petit montant d'abord

1. Complétez une inscription
2. Effectuez un paiement test
3. Vérifiez la redirection vers payment-success.html
4. Vérifiez la réception du paiement sur le dashboard DexpayAfrica

## 🐛 Dépannage Woneko

### Erreur 500 (Internal Server Error)

**Causes possibles** :
- Permissions incorrectes
- Erreur PHP
- Extension manquante

**Solutions** :
1. Vérifiez les logs d'erreur PHP
2. Vérifiez les permissions (755/644)
3. Contactez le support Woneko

### Erreur "Could not resolve host"

**Cause** : Serveur Woneko ne peut pas accéder à api.dexpayafrica.com

**Solutions** :
1. Vérifiez la connexion internet du serveur
2. Contactez le support Woneko pour autoriser api.dexpayafrica.com
3. Vérifiez les règles du pare-feu

### Page Blanche

**Causes possibles** :
- Erreur PHP fatale
- Fichier manquant

**Solutions** :
1. Activez l'affichage des erreurs PHP temporairement
2. Vérifiez les logs d'erreur
3. Vérifiez que tous les fichiers sont uploadés

## 📊 Monitoring Post-Déploiement

### Vérifications Quotidiennes (Première Semaine)

- [ ] Site accessible
- [ ] Formulaire d'inscription fonctionne
- [ ] Redirection DexpayAfrica fonctionne
- [ ] Paiements reçus sur dashboard DexpayAfrica
- [ ] Pas d'erreurs dans les logs

### Logs à Surveiller

```bash
# Via SSH ou panneau de contrôle
tail -f /var/log/apache2/error.log
tail -f /var/log/php-fpm/error.log
```

## 🔄 Mise à Jour Future

### Pour Mettre à Jour le Site

1. **Modifiez** les fichiers en local
2. **Testez** en local (XAMPP)
3. **Créez** un nouveau ZIP
4. **Uploadez** sur Woneko
5. **Remplacez** les fichiers modifiés

### Sauvegarde Avant Mise à Jour

Toujours faire une sauvegarde avant :
```bash
# Via FTP : Télécharger tout le dossier public_html/
# Via panneau : Utiliser l'outil de backup Woneko
```

## 📞 Support

### Support Woneko
- **Email** : support@woneko.com (vérifiez l'email exact)
- **Téléphone** : Consultez votre compte Woneko
- **Documentation** : https://woneko.com/docs (si disponible)

### Support DexpayAfrica
- **Dashboard** : https://dashboard.dexpayafrica.com
- **Email** : support@dexpayafrica.com
- **Documentation** : https://docs.dexpayafrica.com

## ✅ Checklist de Déploiement

### Avant le Déploiement
- [ ] ZIP créé avec tous les fichiers
- [ ] Mode production activé (SIMULATION_MODE = false)
- [ ] Clés API DexpayAfrica configurées
- [ ] Documentation incluse

### Pendant le Déploiement
- [ ] Fichiers uploadés sur Woneko
- [ ] Structure de dossiers correcte
- [ ] Permissions configurées (755/644)
- [ ] Extensions PHP activées (cURL, JSON, OpenSSL)

### Après le Déploiement
- [ ] Site accessible via https://linekode.com
- [ ] SSL/HTTPS activé
- [ ] Test de connexion DexpayAfrica réussi
- [ ] Formulaire d'inscription testé
- [ ] Paiement test effectué et validé
- [ ] Logs vérifiés (pas d'erreurs)

## 🎯 Résumé des Étapes

1. **Connectez-vous** à Woneko
2. **Uploadez** linekode-final-v2.zip
3. **Décompressez** dans public_html/
4. **Configurez** les permissions
5. **Activez** SSL/HTTPS
6. **Testez** la connexion DexpayAfrica
7. **Testez** un paiement réel
8. **Surveillez** les logs

## 🎉 Lancement en Production

Une fois tous les tests validés :
- ✅ Annoncez le lancement
- ✅ Surveillez les premières inscriptions
- ✅ Vérifiez les paiements
- ✅ Collectez les retours utilisateurs

**Votre site Linekode est prêt pour le déploiement sur Woneko !** 🚀

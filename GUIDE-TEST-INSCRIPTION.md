# 🧪 GUIDE DE TEST - FORMULAIRE D'INSCRIPTION

**Date** : 28 Mars 2026  
**Problème** : Le formulaire d'inscription ne s'affiche pas

---

## 🔍 DIAGNOSTIC

### **Vérifications Effectuées**
✅ Tous les liens dans les pages HTML pointent vers `inscription.php`  
✅ Le fichier `inscription.php` existe (39 KB)  
✅ Les permissions du fichier sont correctes  
✅ Les meta tags ont été corrigés (inscription.php au lieu de inscription.html)

---

## 🧪 TESTS À EFFECTUER

### **Test 1 : Accès Direct au Fichier**

**Sur Woneko** :
1. Ouvrez votre navigateur
2. Allez directement à : `https://linekode.com/inscription.php`
3. **Résultat attendu** : Le formulaire d'inscription doit s'afficher

**Si le formulaire s'affiche** :
- ✅ Le fichier PHP fonctionne correctement
- ❌ Le problème vient des liens dans les autres pages

**Si le formulaire ne s'affiche PAS** :
- ❌ Problème avec le fichier PHP ou la configuration du serveur
- Vérifiez les erreurs dans les logs du serveur

### **Test 2 : Vérifier les Liens**

**Depuis la page d'accueil** :
1. Allez sur `https://linekode.com/`
2. Cliquez sur le bouton "Inscrivez-vous" dans le menu
3. **Résultat attendu** : Redirection vers `inscription.php`

**Depuis la page formations** :
1. Allez sur `https://linekode.com/formations.html`
2. Cliquez sur "S'inscrire" sous une formation
3. **Résultat attendu** : Redirection vers `inscription.php?formation=frontend`

### **Test 3 : Vérifier les Erreurs**

**Ouvrez la console du navigateur (F12)** :
1. Allez sur `https://linekode.com/inscription.php`
2. Ouvrez F12 → Console
3. Regardez s'il y a des erreurs en rouge

**Erreurs possibles** :
- `404 Not Found` → Le fichier n'existe pas sur le serveur
- `500 Internal Server Error` → Erreur PHP
- `403 Forbidden` → Problème de permissions

---

## 🔧 SOLUTIONS SELON LE PROBLÈME

### **Problème 1 : Erreur 404 (Fichier non trouvé)**

**Cause** : Le fichier `inscription.php` n'a pas été uploadé correctement

**Solution** :
1. Vérifiez que `inscription.php` est bien dans `public_html/`
2. Vérifiez que le nom est exact (pas `Inscription.php` ou `inscription.PHP`)
3. Re-uploadez le fichier si nécessaire

### **Problème 2 : Erreur 500 (Erreur serveur)**

**Cause** : Erreur PHP dans le code

**Solution** :
1. Vérifiez les logs d'erreur PHP sur Woneko
2. Vérifiez que PHP est activé sur le serveur
3. Testez avec un fichier PHP simple :
```php
<?php
phpinfo();
?>
```

### **Problème 3 : Page Blanche**

**Cause** : Erreur PHP qui n'affiche rien

**Solution** :
1. Activez l'affichage des erreurs PHP :
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```
2. Ajoutez cette ligne au début de `inscription.php`
3. Rechargez la page pour voir les erreurs

### **Problème 4 : Redirection Infinie**

**Cause** : Problème de configuration .htaccess

**Solution** :
1. Vérifiez s'il y a un fichier `.htaccess` dans `public_html/`
2. Vérifiez les règles de redirection
3. Désactivez temporairement `.htaccess` pour tester

---

## 📋 CHECKLIST DE DÉPLOIEMENT

Avant de tester, assurez-vous que :

- [ ] Le fichier `inscription.php` est dans `public_html/`
- [ ] Le fichier `inscription.html` a été supprimé (pour éviter la confusion)
- [ ] Les dossiers `css/`, `js/`, `images/`, `api/` sont présents
- [ ] Les permissions sont correctes (644 pour les fichiers, 755 pour les dossiers)
- [ ] Le cache du navigateur a été vidé (Ctrl+F5)

---

## 🚀 DÉPLOIEMENT CORRECT

### **Structure Attendue sur Woneko** :
```
public_html/
├── index.html
├── about.html
├── formations.html
├── contact.html
├── inscription.php          ← DOIT ÊTRE LÀ
├── payment-success.php
├── payment-cancelled.php
├── css/
│   ├── style.css
│   ├── payment-styles.css
│   ├── operator-selection.css
│   └── ...
├── js/
│   └── script.js
├── images/
│   └── ...
└── api/
    ├── dexpay-checkout.php
    └── ...
```

---

## 📸 INFORMATIONS À ENVOYER

Si le problème persiste, envoyez-moi :

1. **Capture d'écran** de ce que vous voyez quand vous cliquez sur "Inscription"
2. **Capture d'écran** de la console (F12) avec les erreurs
3. **URL exacte** affichée dans la barre d'adresse
4. **Message d'erreur** exact (si erreur)

Avec ces informations, je pourrai identifier et corriger le problème exact ! 🎯

# 💳 Activer les Paiements Réels DexpayAfrica

## ⚠️ IMPORTANT - Lisez Ceci en Premier

**NE PAS activer les paiements réels tant que Woneko ne peut pas accéder à DexpayAfrica !**

Si vous activez les paiements réels alors que Woneko bloque toujours l'API, vous aurez :
- ❌ Chargement infini
- ❌ Utilisateurs bloqués
- ❌ Aucun paiement traité

## 🔍 Étape 1 : Tester la Connexion

### **Test Obligatoire**

Accédez à cette URL dans votre navigateur :
```
https://linekode.com/api/test-dexpay-connection.php
```

### **Résultat Requis pour Continuer**

Vous DEVEZ voir ceci :
```json
{
  "summary": {
    "status": "success",
    "message": "✅ Connexion DexpayAfrica fonctionnelle !"
  },
  "api_tests": {
    "dns_resolution": {
      "status": "success",
      "ip_address": "xxx.xxx.xxx.xxx"
    },
    "https_connection": {
      "status": "success",
      "http_code": 200
    },
    "session_creation": {
      "status": "success",
      "session_id": "sess_xxxxx"
    }
  }
}
```

### **Si Vous Voyez Ceci - STOP !**

```json
{
  "dns_resolution": {
    "status": "failed",
    "error": "DNS resolution failed"
  }
}
```

**Woneko bloque toujours DexpayAfrica. NE PAS continuer.**

Actions :
1. Contactez le support Woneko
2. Demandez l'autorisation pour `api.dexpayafrica.com`
3. Attendez leur réponse
4. Retestez après leur intervention

## ✅ Étape 2 : Activer le Mode Production

**SEULEMENT si le test de l'Étape 1 a réussi !**

### **Option A : Via le Gestionnaire de Fichiers Woneko**

1. Connectez-vous au panneau Woneko
2. Ouvrez le gestionnaire de fichiers
3. Naviguez vers `public_html/api/`
4. Ouvrez le fichier `dexpay-checkout.php`
5. Trouvez la ligne 20 :
   ```php
   define('SIMULATION_MODE', true);
   ```
6. Changez en :
   ```php
   define('SIMULATION_MODE', false);
   ```
7. Sauvegardez le fichier

### **Option B : Via FTP**

1. Connectez-vous avec FileZilla
2. Téléchargez `public_html/api/dexpay-checkout.php`
3. Ouvrez avec un éditeur de texte
4. Ligne 20 : Changez `true` en `false`
5. Sauvegardez
6. Re-uploadez le fichier

### **Option C : Uploader le Nouveau ZIP**

J'ai créé un nouveau ZIP avec mode production activé :
- Fichier : `linekode-production-paiements-reels.zip`
- Emplacement : `C:\xampp\htdocs\`

Upload et décompressez ce fichier sur Woneko.

## 🧪 Étape 3 : Tester les Paiements Réels

### **Test 1 : Inscription**

1. Allez sur `https://linekode.com/inscription.html`
2. Remplissez le formulaire avec de vraies données
3. Validez

**Résultat attendu :**
- Message "Redirection vers le paiement..."
- Redirection vers DexpayAfrica (domaine dexpayafrica.com)
- Page de paiement DexpayAfrica s'affiche

### **Test 2 : Paiement Test**

⚠️ **Utilisez un petit montant pour tester**

1. Sur la page DexpayAfrica, choisissez une méthode de paiement
2. Entrez vos informations
3. Effectuez le paiement test

**Résultat attendu :**
- Paiement traité par DexpayAfrica
- Redirection vers `https://linekode.com/payment-success.html`
- Confirmation affichée

### **Test 3 : Vérifier sur Dashboard DexpayAfrica**

1. Connectez-vous à `https://dashboard.dexpayafrica.com`
2. Allez dans "Transactions" ou "Paiements"
3. Vérifiez que le paiement test apparaît

**Résultat attendu :**
- Transaction visible
- Statut : "Completed" ou "Success"
- Montant : 50 000 XOF

## 📊 Monitoring des Paiements

### **Vérifications Quotidiennes**

- [ ] Tous les paiements apparaissent sur le dashboard DexpayAfrica
- [ ] Aucune erreur dans les logs Woneko
- [ ] Les utilisateurs sont redirigés correctement
- [ ] Les confirmations de paiement fonctionnent

### **Logs à Surveiller**

Sur Woneko, consultez régulièrement :
- Logs d'erreur PHP
- Logs d'accès
- Cherchez les erreurs liées à DexpayAfrica

## 🔄 Retour au Mode Simulation

Si vous rencontrez des problèmes en production :

1. Ouvrez `api/dexpay-checkout.php`
2. Ligne 20 : Changez `false` en `true`
3. Sauvegardez
4. Le site repassera en mode simulation

## ⚠️ Problèmes Courants

### **Problème : Chargement infini après activation**

**Cause :** Woneko bloque toujours DexpayAfrica

**Solution :**
1. Retournez en mode simulation
2. Retestez `test-dexpay-connection.php`
3. Contactez Woneko si le test échoue

### **Problème : Erreur 401 Unauthorized**

**Cause :** Clés API invalides

**Solution :**
1. Vérifiez sur `https://dashboard.dexpayafrica.com`
2. Vérifiez que les clés sont actives
3. Régénérez les clés si nécessaire
4. Mettez à jour `dexpay-checkout.php`

### **Problème : Paiement non reçu**

**Cause :** Webhook non configuré ou erreur de redirection

**Solution :**
1. Vérifiez les webhooks sur le dashboard DexpayAfrica
2. Vérifiez les URLs de redirection
3. Consultez les logs DexpayAfrica

## 🔐 Sécurité en Production

### **Checklist Sécurité**

- [ ] SSL/HTTPS activé sur linekode.com
- [ ] Clés API stockées de manière sécurisée
- [ ] Pas de clés API dans le code frontend
- [ ] Logs d'erreur non accessibles publiquement
- [ ] Permissions fichiers correctes (755/644)

### **Bonnes Pratiques**

1. **Ne jamais** exposer la clé secrète publiquement
2. **Toujours** utiliser HTTPS
3. **Surveiller** les transactions régulièrement
4. **Sauvegarder** les données régulièrement
5. **Tester** avant chaque mise à jour

## 📞 Support

### **Si Problème avec DexpayAfrica**
- Dashboard : https://dashboard.dexpayafrica.com
- Email : support@dexpayafrica.com
- Documentation : https://docs.dexpayafrica.com

### **Si Problème avec Woneko**
- Panneau de contrôle Woneko
- Support Woneko (email dans votre compte)

## ✅ Checklist Finale

### **Avant Activation**
- [ ] Test de connexion réussi (`test-dexpay-connection.php`)
- [ ] DNS resolution = success
- [ ] HTTPS connection = success
- [ ] Session creation = success
- [ ] SSL activé sur linekode.com

### **Pendant Activation**
- [ ] Mode simulation désactivé (false)
- [ ] Fichier sauvegardé et uploadé
- [ ] Cache navigateur vidé

### **Après Activation**
- [ ] Test d'inscription effectué
- [ ] Redirection vers DexpayAfrica fonctionne
- [ ] Paiement test effectué et validé
- [ ] Transaction visible sur dashboard DexpayAfrica
- [ ] Page de succès s'affiche correctement

## 🎯 Résumé

**Pour activer les paiements réels :**

1. ✅ **Testez** : `test-dexpay-connection.php` doit retourner "success"
2. ✅ **Modifiez** : `SIMULATION_MODE` de `true` à `false`
3. ✅ **Testez** : Effectuez un paiement test complet
4. ✅ **Vérifiez** : Transaction visible sur dashboard DexpayAfrica
5. ✅ **Surveillez** : Logs et transactions quotidiennement

**Ne passez en production que si le test de connexion réussit !** 🎯

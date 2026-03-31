# 🔧 CONFIGURATION DES OPÉRATEURS DE PAIEMENT

**Système de liens de paiement directs - Sans API DexpayAfrica**

---

## 📋 CONFIGURATION ACTUELLE

Le fichier `api/create-payment-link.php` contient la configuration des opérateurs.

### **Wave** ✅ (Configuré)
```php
'wave' => [
    'name' => 'Wave',
    'merchant_id' => 'cos-23x70p69010nj', // Votre ID marchand Wave
    'url_template' => 'https://pay.wave.com/c/{merchant_id}?a={amount}&c=XOF&m={description}'
]
```

**Format de l'URL** :
```
https://pay.wave.com/c/cos-23x70p69010nj?a=50000&c=XOF&m=Inscription%20Formation%20Linekode
```

---

## 🔧 OPÉRATEURS À CONFIGURER

### **Orange Money** ⚠️ (À configurer)
```php
'orange_money' => [
    'name' => 'Orange Money',
    'merchant_id' => 'VOTRE_ID_ORANGE', // À remplacer
    'url_template' => 'https://payment.orange-money.com/pay?merchant={merchant_id}&amount={amount}&currency=XOF&description={description}'
]
```

**Action requise** :
1. Créez un compte marchand Orange Money
2. Obtenez votre ID marchand
3. Remplacez `VOTRE_ID_ORANGE` dans le fichier
4. Vérifiez le format de l'URL avec Orange Money

---

### **MTN Mobile Money** ⚠️ (À configurer)
```php
'mtn' => [
    'name' => 'MTN Mobile Money',
    'merchant_id' => 'VOTRE_ID_MTN', // À remplacer
    'url_template' => 'https://mtn-momo.com/pay?merchant={merchant_id}&amount={amount}&currency=XOF&description={description}'
]
```

**Action requise** :
1. Créez un compte marchand MTN Mobile Money
2. Obtenez votre ID marchand
3. Remplacez `VOTRE_ID_MTN` dans le fichier
4. Vérifiez le format de l'URL avec MTN

---

### **Moov Money** ⚠️ (À configurer)
```php
'moov' => [
    'name' => 'Moov Money',
    'merchant_id' => 'VOTRE_ID_MOOV', // À remplacer
    'url_template' => 'https://moov-money.com/pay?merchant={merchant_id}&amount={amount}&currency=XOF&description={description}'
]
```

**Action requise** :
1. Créez un compte marchand Moov Money
2. Obtenez votre ID marchand
3. Remplacez `VOTRE_ID_MOOV` dans le fichier
4. Vérifiez le format de l'URL avec Moov

---

## 📝 COMMENT CONFIGURER UN OPÉRATEUR

### **Étape 1 : Obtenir l'ID Marchand**

Pour chaque opérateur :
1. Créez un compte marchand sur leur plateforme
2. Complétez la vérification KYC (Know Your Customer)
3. Obtenez votre ID marchand unique

### **Étape 2 : Vérifier le Format de l'URL**

Chaque opérateur a son propre format d'URL. Contactez leur support pour obtenir :
- Le format exact de l'URL de paiement
- Les paramètres requis (montant, devise, description, etc.)
- Des exemples d'URLs valides

### **Étape 3 : Mettre à Jour le Code**

Éditez `api/create-payment-link.php` :
```php
'nom_operateur' => [
    'name' => 'Nom Affiché',
    'merchant_id' => 'VOTRE_ID_REEL',
    'url_template' => 'https://url-operateur.com/pay?...'
]
```

### **Étape 4 : Tester**

1. Allez sur `inscription.php`
2. Remplissez le formulaire
3. Sélectionnez l'opérateur
4. Vérifiez que l'URL générée est correcte
5. Testez le paiement avec un petit montant

---

## 🧪 TEST RAPIDE

Pour tester si un opérateur est bien configuré :

```bash
# Créez un fichier test-operator.php
<?php
require_once 'api/create-payment-link.php';

$test = [
    'operator' => 'wave',
    'amount' => 1000,
    'description' => 'Test'
];

// Appelez l'API et vérifiez l'URL générée
```

---

## ⚠️ IMPORTANT

### **Opérateurs Non Configurés**

Si un utilisateur sélectionne un opérateur non configuré (Orange, MTN, Moov), le système retournera une erreur.

**Pour désactiver temporairement un opérateur** :

Éditez `inscription.php` et commentez l'opérateur dans la grille :
```html
<!-- Désactivé temporairement
<div class="operator-card" onclick="selectOperator('orange_money')">
    ...
</div>
-->
```

---

## 📞 CONTACTS SUPPORT

### **Wave**
- Site : https://www.wave.com/sn/
- Support marchand : Via l'application Wave Business

### **Orange Money**
- Site : https://www.orange-money.com/
- Support : Contactez votre agence Orange

### **MTN Mobile Money**
- Site : https://www.mtn.com/
- Support : Contactez votre agence MTN

### **Moov Money**
- Site : https://www.moov-africa.sn/
- Support : Contactez votre agence Moov

---

## 🎯 PROCHAINES ÉTAPES

1. **Wave** : ✅ Déjà configuré et fonctionnel
2. **Orange Money** : Obtenez votre ID marchand
3. **MTN Mobile Money** : Obtenez votre ID marchand
4. **Moov Money** : Obtenez votre ID marchand

Une fois tous les opérateurs configurés, votre système de paiement sera complet ! 🚀

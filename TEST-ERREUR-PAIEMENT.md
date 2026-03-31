# 🔧 TEST DE L'ERREUR DE PAIEMENT

**Date** : 28 Mars 2026  
**Objectif** : Identifier et corriger l'erreur de paiement

---

## 🧪 ÉTAPE 1 : Test de Réception des Données

J'ai créé un script de test : **`api/test-payment-data.php`**

### **Comment tester** :

1. **Ouvrez** `http://localhost/linekode/inscription.php`
2. **Ouvrez la console** (F12)
3. **Modifiez temporairement** le code JavaScript :

```javascript
// Dans inscription.php, ligne ~705
// Remplacer :
const sessionResponse = await fetch('api/dexpay-checkout.php', {

// Par :
const sessionResponse = await fetch('api/test-payment-data.php', {
```

4. **Remplissez le formulaire** et cliquez sur "Payer maintenant"
5. **Regardez la console** - vous verrez toutes les données reçues par le serveur

### **Ce que vous devriez voir** :

```json
{
  "success": true,
  "message": "Test de réception des données",
  "debug": {
    "timestamp": "2026-03-28 01:42:00",
    "method": "POST",
    "headers": {
      "content_type": "application/json",
      "content_length": "250"
    },
    "php_input": "{\"amount\":50000,\"customer_phone\":\"+221771234567\",\"customer_email\":\"test@example.com\",\"description\":\"Inscription Formation Linekode\",\"success_url\":\"...\",\"cancel_url\":\"...\",\"metadata\":{...}}",
    "raw_input_length": 250,
    "json_decode_result": {
      "amount": 50000,
      "customer_phone": "+221771234567",
      "customer_email": "test@example.com",
      "description": "Inscription Formation Linekode",
      "success_url": "...",
      "cancel_url": "...",
      "metadata": {...}
    },
    "json_error": null
  }
}
```

---

## 🔍 ÉTAPE 2 : Analyser les Résultats

### **Si `json_decode_result` contient les données** :
✅ Le problème n'est PAS la transmission des données  
❌ Le problème est dans la validation ou le traitement

### **Si `json_decode_result` est null** :
❌ Le problème est la transmission des données  
✅ Vérifier `json_error` pour voir l'erreur exacte

### **Si `php_input` est vide** :
❌ Le problème est que le serveur ne reçoit rien  
✅ Vérifier la configuration PHP ou les headers

---

## 🛠️ ÉTAPE 3 : Solutions Selon le Problème

### **Problème 1 : JSON invalide**

**Solution** :
```javascript
// Vérifier que le payload est bien formaté
console.log('Payload avant envoi:', JSON.stringify(sessionPayload, null, 2));
```

### **Problème 2 : Headers incorrects**

**Solution** :
```javascript
// Ajouter plus de headers
const sessionResponse = await fetch('api/dexpay-checkout.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify(sessionPayload)
});
```

### **Problème 3 : Configuration PHP**

**Solution** :
```php
// Vérifier php.ini
allow_url_fopen = On
max_input_vars = 1000
post_max_size = 8M
upload_max_filesize = 2M
```

### **Problème 4 : CORS bloqué**

**Solution** :
```php
// Ajouter plus de headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');
header('Access-Control-Max-Age: 86400');
```

---

## 📋 CHECKLIST DE DÉBOGAGE

- [ ] Tester avec `test-payment-data.php`
- [ ] Vérifier que les données sont reçues
- [ ] Vérifier que le JSON est valide
- [ ] Vérifier les headers HTTP
- [ ] Vérifier la configuration PHP
- [ ] Tester sur Woneko (pas seulement en local)

---

## 🚀 APRÈS LE TEST

**Envoyez-moi** :
1. La réponse complète de `test-payment-data.php`
2. Une capture d'écran de la console
3. Le message d'erreur exact (si erreur)

Avec ces informations, je pourrai corriger le problème exact ! 🎯

# 🔧 SOLUTION - API DEXPAYAFRICA NE S'OUVRE PAS

**Problème** : L'API DexpayAfrica ne répond pas ou ne s'ouvre pas

---

## 🧪 TESTS À EFFECTUER

### **Test 1 : Test Simple**

Allez sur : `https://linekode.com/test-api-simple.php`

Ce test affichera en texte brut :
- ✅ ou ❌ pour chaque étape
- Les erreurs exactes
- La réponse de l'API

**C'est le test le plus simple pour diagnostiquer le problème.**

---

### **Test 2 : Test Complet JSON**

Allez sur : `https://linekode.com/api/test-dexpay-connection.php`

Ce test retourne un JSON complet avec tous les détails.

---

## 🔍 PROBLÈMES POSSIBLES

### **Problème 1 : Clés API Invalides**

**Symptôme** : HTTP 401 Unauthorized

**Solution** :
1. Allez sur https://portal.dexpay.africa/api-keys
2. Vérifiez que vos clés sont actives
3. Copiez les nouvelles clés si nécessaire
4. Mettez à jour dans les fichiers :
   - `api/dexpay-checkout.php`
   - `api/dexpay-create-attempt.php`
   - `api/test-dexpay-connection.php`

---

### **Problème 2 : URL API Incorrecte**

**Symptôme** : HTTP 404 Not Found

**Vérifiez** :
- URL de base : `https://api.dexpay.africa/api/v1`
- Endpoint : `/checkout-sessions` (avec tiret, pas slash)

**Correction** :
```php
// ✅ CORRECT
$ch = curl_init('https://api.dexpay.africa/api/v1/checkout-sessions');

// ❌ INCORRECT
$ch = curl_init('https://api.dexpay.africa/api/v1/checkout/sessions');
```

---

### **Problème 3 : Serveur Bloque les Connexions Sortantes**

**Symptôme** : Timeout ou erreur de connexion

**Solution** :
1. Contactez Woneko
2. Demandez d'autoriser les connexions sortantes vers :
   - `api.dexpay.africa`
   - Port 443 (HTTPS)

---

### **Problème 4 : cURL Non Disponible**

**Symptôme** : "cURL non disponible"

**Solution** :
1. Activez l'extension PHP cURL
2. Dans `php.ini`, décommentez :
   ```
   extension=curl
   ```
3. Redémarrez le serveur web

---

### **Problème 5 : Payload Incorrect**

**Symptôme** : HTTP 400 Bad Request

**Vérifiez le payload** :
```php
[
    'reference' => 'LINEKODE_...',  // Requis, unique
    'item_name' => 'Description',    // Requis
    'amount' => 50000,               // Requis, en XOF
    'currency' => 'XOF',             // Requis
    'countryISO' => 'SN',            // Requis
    'webhook_url' => '',             // Optionnel
    'success_url' => 'https://...',  // Requis
    'failure_url' => 'https://...'   // Requis
]
```

---

## 📋 CHECKLIST DE VÉRIFICATION

- [ ] Test simple effectué (`test-api-simple.php`)
- [ ] cURL disponible
- [ ] Connexion HTTPS fonctionne
- [ ] Clés API valides et actives
- [ ] URL correcte (`/checkout-sessions`)
- [ ] Header correct (`x-api-key`)
- [ ] Payload complet et valide
- [ ] Serveur autorise connexions sortantes

---

## 🆘 SI RIEN NE FONCTIONNE

**Effectuez le test simple** : `https://linekode.com/test-api-simple.php`

**Envoyez-moi** :
1. Le résultat complet du test (copier-coller le texte)
2. Le message d'erreur exact
3. Le code HTTP retourné

Avec ces informations, je pourrai identifier le problème exact et le corriger ! 🎯

---

## 📞 SUPPORT DEXPAYAFRICA

Si le problème vient de leur côté :
- **Portail** : https://portal.dexpay.africa
- **Documentation** : https://docs.dexpay.africa
- **Support** : Contactez via le portail

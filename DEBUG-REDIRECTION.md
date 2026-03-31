# 🔍 DEBUG - PROBLÈME DE REDIRECTION

**Problème** : La redirection vers DexpayAfrica ne s'ouvre pas

---

## 📊 POINTS DE VÉRIFICATION

### **1. Vérifier que l'API fonctionne**

Testez : `https://linekode.com/api/test-dexpay-connection.php`

**Si échec** → L'API ne fonctionne pas, corrigez d'abord l'API
**Si succès** → Passez au point 2

---

### **2. Vérifier les logs de la console**

Ouvrez F12 → Console et cherchez :

#### **Étape 1 : Création de session**
```
📤 Étape 1: Création session
📥 Réponse session: {...}
```

**Vérifiez** :
- `success: true` ✅
- `reference: "LINEKODE_..."` ✅ (PAS "LINEKODE_SIM_...")
- `session_id` présent ✅

**Si erreur ici** → Le problème est dans `api/dexpay-checkout.php`

---

#### **Étape 2 : Création de tentative**
```
📤 Étape 2: Création tentative de paiement
📥 Réponse tentative: {...}
```

**Vérifiez** :
- `success: true` ✅
- `data.cashout_url` présent ✅
- URL commence par `https://` ✅

**Si erreur ici** → Le problème est dans `api/dexpay-create-attempt.php`

---

#### **Étape 3 : Redirection**
```
✅ Redirection vers: https://...
```

**Si ce log apparaît mais pas de redirection** :
- Popup bloquée par le navigateur
- JavaScript bloqué
- Erreur silencieuse

---

## 🔧 SOLUTIONS SELON LE PROBLÈME

### **Problème A : Erreur à l'étape 1 (Session)**

**Erreur possible** : "Erreur API DexpayAfrica"

**Causes** :
1. URL API incorrecte
2. Clés API invalides
3. Payload incorrect

**Solution** :
1. Vérifiez les logs PHP sur Woneko
2. Vérifiez que l'URL est `/checkout-sessions` (avec tiret)
3. Vérifiez les clés API sur https://portal.dexpay.africa/api-keys

---

### **Problème B : Erreur à l'étape 2 (Tentative)**

**Erreur possible** : "Cannot POST .../attempts"

**Causes** :
1. Référence de session invalide
2. Opérateur non supporté
3. Payload incorrect

**Solution** :
1. Vérifiez que la référence n'est PAS "LINEKODE_SIM_..."
2. Vérifiez que l'opérateur est valide (wave, orange_money, mtn, moov)
3. Vérifiez les logs dans `api/dexpay-create-attempt.php`

---

### **Problème C : Pas de redirection malgré succès**

**Symptôme** : Logs montrent succès mais pas de redirection

**Causes** :
1. `cashout_url` est vide ou null
2. Popup bloquée
3. Erreur JavaScript silencieuse

**Solution** :
```javascript
// Vérifiez dans la console
console.log('cashout_url:', attemptResult.data.cashout_url);

// Si vide ou null → Problème API
// Si présent → Problème navigateur
```

---

## 🧪 TEST MANUEL

### **Test 1 : Vérifier la réponse de l'API**

Dans la console, après avoir cliqué sur un opérateur :
```javascript
// Cherchez le log "📥 Réponse tentative:"
// Copiez l'objet complet
// Vérifiez :
{
  success: true,
  data: {
    cashout_url: "https://..." // DOIT ÊTRE PRÉSENT
  }
}
```

### **Test 2 : Forcer la redirection**

Si `cashout_url` est présent mais pas de redirection, testez manuellement :
```javascript
// Dans la console, tapez :
window.location.href = "URL_COPIÉE_ICI"
```

Si ça fonctionne → Problème dans le code
Si ça ne fonctionne pas → Problème navigateur

---

## 📋 CHECKLIST DE DIAGNOSTIC

- [ ] API test fonctionne (test-dexpay-connection.php)
- [ ] Console ouverte (F12)
- [ ] Formulaire rempli et validé
- [ ] Opérateur sélectionné
- [ ] Log "📤 Étape 1" visible
- [ ] Log "📥 Réponse session" avec success: true
- [ ] Log "📤 Étape 2" visible
- [ ] Log "📥 Réponse tentative" avec success: true
- [ ] Log "✅ Redirection vers" visible
- [ ] cashout_url présent et valide
- [ ] Redirection effectuée

---

## 🆘 SI RIEN NE FONCTIONNE

**Envoyez-moi** :
1. Capture d'écran de la console complète (F12)
2. Résultat de test-dexpay-connection.php
3. Message d'erreur exact (si erreur)

Avec ces informations, je pourrai identifier le problème exact !

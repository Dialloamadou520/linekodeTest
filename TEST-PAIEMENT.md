# 🧪 TEST DU PAIEMENT DEXPAYAFRICA

## 📋 Instructions de Test

### 1. Test Local (XAMPP)

1. **Démarrez XAMPP**
   - Apache doit être actif

2. **Ouvrez le navigateur**
   - URL : `http://localhost/linekode/inscription.html`
   - Ouvrez la console (F12)

3. **Remplissez le formulaire**
   - Prénom : Test
   - Nom : Utilisateur
   - Email : test@example.com
   - Téléphone : +221771234567
   - Niveau : Débutant
   - Cochez "J'accepte les conditions"

4. **Cliquez sur "Valider mon inscription"**
   - Le formulaire doit disparaître
   - Le bouton "Payer maintenant" doit apparaître

5. **Cliquez sur "Payer maintenant"**
   - Vérifiez la console :
     - 📤 Envoi session payload (doit contenir amount, customer_phone, customer_email)
     - 📥 Réponse session (doit contenir success: true, checkout_url)
     - ✅ Redirection vers (doit afficher l'URL DexpayAfrica)

6. **Résultat attendu**
   - Redirection vers `https://pay.dexpay.africa/...`
   - Page DexpayAfrica avec tous les opérateurs disponibles

### 2. Test sur Woneko

1. **Uploadez le ZIP**
   - `linekode-simplified-final.zip`

2. **Décompressez**
   - Dans `public_html/`

3. **Testez**
   - URL : `https://linekode.com/inscription.html`
   - Suivez les mêmes étapes que le test local

## ❌ Problèmes Possibles

### Problème 1 : "Données manquantes"
**Cause** : Le backend ne reçoit pas les données correctement

**Solution** :
- Vérifiez que `customer_phone` et `customer_email` sont bien dans le payload
- Regardez les logs console : `📤 Envoi session payload`
- Vérifiez que le backend reçoit les données : logs PHP

### Problème 2 : "URL de paiement non reçue"
**Cause** : L'API DexpayAfrica ne retourne pas `checkout_url`

**Solution** :
- Vérifiez les logs console : `📥 Réponse session`
- Vérifiez que `sessionResult.checkout_url` existe
- Vérifiez que l'API DexpayAfrica fonctionne

### Problème 3 : Pas de redirection
**Cause** : JavaScript bloqué ou erreur silencieuse

**Solution** :
- Ouvrez la console (F12)
- Cherchez les erreurs en rouge
- Vérifiez que `window.location.href` est appelé

## 📊 Logs Attendus dans la Console

```
📤 Envoi session payload: {
  amount: 50000,
  customer_phone: "+221771234567",
  customer_email: "test@example.com",
  description: "Inscription Formation Linekode",
  success_url: "https://linekode.com/payment-success.html",
  cancel_url: "https://linekode.com/payment-cancelled.html",
  metadata: {...}
}

📥 Réponse session: {
  success: true,
  session_id: "...",
  reference: "LINEKODE_...",
  checkout_url: "https://pay.dexpay.africa/...",
  data: {...}
}

✅ Redirection vers: https://pay.dexpay.africa/...
```

## 🔧 Débogage

Si le paiement ne fonctionne pas :

1. **Capturez la console complète**
   - F12 → Console
   - Clic droit → "Save as..."

2. **Vérifiez les logs PHP**
   - Sur Woneko : Panneau de contrôle → Logs
   - En local : `C:\xampp\apache\logs\error.log`

3. **Envoyez-moi**
   - Capture d'écran de la console
   - Message d'erreur exact
   - Logs PHP si disponibles

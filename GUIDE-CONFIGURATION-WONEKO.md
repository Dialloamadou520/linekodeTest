# 🔧 Guide de Configuration Woneko pour DexpayAfrica

## 🎯 Objectif

Configurer Woneko pour autoriser l'accès à `api.dexpayafrica.com` et activer les paiements réels.

## 📋 Étape 1 : Connexion à Woneko

1. **Ouvrez** votre navigateur
2. **Accédez** au site de Woneko (URL fournie lors de votre inscription)
3. **Connectez-vous** avec vos identifiants :
   - Email ou nom d'utilisateur
   - Mot de passe

## 🔍 Étape 2 : Localiser les Paramètres PHP

### **Cherchez l'une de ces sections :**

- "PHP Settings" ou "Paramètres PHP"
- "PHP Configuration"
- "Select PHP Version" ou "Sélectionner version PHP"
- "PHP Extensions" ou "Extensions PHP"
- "Advanced" → "PHP Settings"

### **Navigation typique :**

```
Panneau de contrôle → Software → Select PHP Version
OU
Panneau de contrôle → Advanced → PHP Configuration
OU
Panneau de contrôle → Settings → PHP Settings
```

## ✅ Étape 3 : Vérifier les Extensions PHP

Assurez-vous que ces extensions sont **activées** (cochées) :

- [x] **cURL** - Pour les appels API
- [x] **JSON** - Pour le traitement des données
- [x] **OpenSSL** - Pour HTTPS
- [x] **mbstring** - Pour le traitement des chaînes
- [x] **fileinfo** - Pour les fichiers

**Note :** Ces extensions sont probablement déjà activées (votre test montre qu'elles le sont).

## 🌐 Étape 4 : Configurer le Pare-feu / Whitelist

C'est **l'étape la plus importante** pour résoudre le problème.

### **Cherchez l'une de ces sections :**

- "Firewall" ou "Pare-feu"
- "Whitelist" ou "Liste blanche"
- "Outbound Connections" ou "Connexions sortantes"
- "API Access" ou "Accès API"
- "Security" → "Firewall Rules"
- "Network" → "Allowed Domains"

### **Si vous trouvez cette section :**

1. **Ajoutez** un nouveau domaine autorisé
2. **Domaine** : `api.dexpayafrica.com`
3. **Port** : `443` (HTTPS)
4. **Type** : Connexion sortante (Outbound)
5. **Sauvegardez** les modifications

### **Si vous ne trouvez PAS cette section :**

Woneko ne permet probablement pas de configurer le pare-feu via le panneau.
→ Vous devez **contacter le support Woneko** (voir Étape 6).

## 🔒 Étape 5 : Vérifier les Restrictions Réseau

### **Cherchez :**

- "Network Restrictions" ou "Restrictions réseau"
- "Blocked Domains" ou "Domaines bloqués"
- "DNS Settings" ou "Paramètres DNS"

### **Vérifiez que :**

- `api.dexpayafrica.com` n'est **PAS** dans la liste des domaines bloqués
- Les connexions sortantes HTTPS sont **autorisées**
- Le DNS externe est **activé**

## 📞 Étape 6 : Contacter le Support Woneko

Si vous ne trouvez pas les paramètres de pare-feu, vous **DEVEZ** contacter le support.

### **Comment Contacter le Support :**

#### **Option 1 : Ticket de Support**

1. **Cherchez** "Support" ou "Help" dans le panneau
2. **Cliquez** sur "Create Ticket" ou "Nouveau ticket"
3. **Remplissez** le formulaire (voir modèle ci-dessous)

#### **Option 2 : Email**

Cherchez l'email de support dans :
- Vos emails de bienvenue Woneko
- Section "Contact" du panneau
- Page "Support" du site Woneko

#### **Option 3 : Chat en Direct**

Si disponible, utilisez le chat en direct (plus rapide).

### **Modèle de Message pour le Support :**

```
Objet : Autorisation accès API externe - api.dexpayafrica.com

Bonjour,

Je suis client Woneko avec le domaine linekode.com.

Mon site utilise l'API de paiement DexpayAfrica mais le serveur ne peut 
pas y accéder.

Erreur actuelle :
- DNS resolution failed
- Could not resolve host: api.dexpayafrica.com

Configuration serveur (vérifiée) :
✅ PHP 8.2.30
✅ cURL activé
✅ OpenSSL activé
✅ JSON activé

Le problème vient du pare-feu/réseau qui bloque l'accès externe.

Merci d'autoriser les connexions sortantes vers :
- Domaine : api.dexpayafrica.com
- Port : 443 (HTTPS)
- Type : Connexion sortante (Outbound)
- Protocole : HTTPS

C'est urgent pour mon système de paiement en ligne.

Informations de mon compte :
- Domaine : linekode.com
- Email : [votre email]
- ID client : [si vous l'avez]

Merci de votre aide rapide.

Cordialement,
[Votre nom]
```

## 🧪 Étape 7 : Tester Après Configuration

Après avoir configuré ou après la réponse du support :

1. **Attendez** 5-10 minutes (propagation des changements)
2. **Testez** : `https://linekode.com/api/test-dexpay-connection.php`
3. **Vérifiez** le résultat

### **Résultat Attendu :**

```json
{
  "summary": {
    "status": "success",
    "message": "✅ Connexion DexpayAfrica fonctionnelle !"
  },
  "api_tests": {
    "dns_resolution": {
      "status": "success"
    }
  }
}
```

### **Si le Test Réussit :**

1. ✅ Woneko a autorisé DexpayAfrica !
2. ✅ Activez les paiements réels
3. ✅ Uploadez `linekode-production-paiements-reels.zip`

### **Si le Test Échoue Encore :**

- Attendez la réponse du support Woneko
- Ou considérez un changement d'hébergeur

## 📸 Captures d'Écran Utiles

Lors de votre navigation dans le panneau Woneko, prenez des captures d'écran de :
- Page d'accueil du panneau
- Section PHP Settings
- Section Firewall/Security (si disponible)
- Message d'erreur (si vous en voyez)

Envoyez-moi ces captures et je vous guiderai plus précisément.

## 🔄 Alternatives si Woneko Refuse

### **Option 1 : Hostinger (Recommandé)**

**Pourquoi Hostinger :**
- ✅ Pas de restriction API
- ✅ Support 24/7 en français
- ✅ Migration gratuite
- ✅ 2.99€/mois seulement
- ✅ DexpayAfrica fonctionne immédiatement

**Migration en 3 étapes :**
1. Créez un compte sur https://www.hostinger.fr
2. Uploadez `linekode-production-paiements-reels.zip`
3. Pointez linekode.com vers Hostinger

### **Option 2 : OVH**

- Prix : 3.59€/mois
- Support français
- Pas de restriction

### **Option 3 : Infomaniak**

- Prix : 5.75€/mois
- Support excellent
- Hébergement écologique

## 📊 Checklist de Configuration Woneko

### **Avant de Contacter le Support**

- [ ] Vérifié les paramètres PHP
- [ ] Cherché section Firewall/Whitelist
- [ ] Vérifié les restrictions réseau
- [ ] Pris des captures d'écran
- [ ] Préparé le message de support

### **Après Configuration**

- [ ] Attendu 5-10 minutes
- [ ] Testé test-dexpay-connection.php
- [ ] Vérifié le résultat
- [ ] Activé les paiements réels (si test OK)

### **Si Woneko Refuse**

- [ ] Choisi un nouvel hébergeur
- [ ] Créé le compte
- [ ] Uploadé le site
- [ ] Pointé le domaine
- [ ] Testé les paiements

## 🎯 Résumé

**Ce que vous devez faire :**

1. **Connectez-vous** à Woneko
2. **Cherchez** les paramètres de pare-feu/whitelist
3. **Autorisez** api.dexpayafrica.com
4. **OU contactez** le support Woneko
5. **Testez** après configuration
6. **Si échec** → Changez d'hébergeur

**Temps estimé :**
- Configuration manuelle : 10-15 minutes
- Via support Woneko : 24-72 heures
- Changement hébergeur : 1-2 heures

**Je suis là pour vous guider à chaque étape !** 🚀

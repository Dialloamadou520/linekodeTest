# Linekode - Site d'Inscription avec Paiement DexpayAfrica

Site web pour les inscriptions aux formations Linekode avec intégration de paiement mobile money via DexpayAfrica.

## 🚀 Fonctionnalités

- ✅ Formulaire d'inscription en ligne
- ✅ Paiement sécurisé via DexpayAfrica
- ✅ Support de tous les opérateurs mobile money (Wave, Orange Money, MTN, Moov)
- ✅ Paiement par carte bancaire
- ✅ Webhooks pour confirmation automatique des paiements
- ✅ Pages de succès et d'annulation personnalisées
- ✅ Interface responsive et moderne

## 📋 Prérequis

- PHP 7.4 ou supérieur
- Extension cURL activée
- Serveur web (Apache, Nginx)
- Compte DexpayAfrica avec clés API

## 🔧 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/VOTRE_USERNAME/linekode-PAIEMENT-DIRECT.git
cd linekode-PAIEMENT-DIRECT
```

### 2. Configuration des clés API

Copiez le fichier de configuration exemple :

```bash
cp .env.example .env
```

Éditez `.env` et remplissez vos clés API DexpayAfrica :

```env
DEXPAY_API_KEY=pk_live_VOTRE_CLE_PUBLIQUE
DEXPAY_SECRET_KEY=sk_live_VOTRE_CLE_SECRETE
SITE_URL=https://votre-domaine.com
```

### 3. Créer le fichier de configuration

```bash
cp config.example.php config.php
```

Le fichier `config.php` chargera automatiquement les variables depuis `.env`.

### 4. Permissions

Assurez-vous que le dossier `data/` est accessible en écriture :

```bash
mkdir -p data
chmod 755 data
```

## 🔑 Configuration DexpayAfrica

### Obtenir les clés API

1. Créez un compte sur https://portal.dexpay.africa
2. Récupérez vos clés API dans la section "API Keys"
3. Copiez la clé publique (`pk_live_...`) et la clé secrète (`sk_live_...`)

### Configurer le Webhook

1. Allez sur https://portal.dexpay.africa/webhooks
2. Ajoutez l'URL : `https://votre-domaine.com/api/dexpay-webhook.php`
3. Activez les événements :
   - `checkout.completed`
   - `payment.success`
   - `payment.failed`
   - `checkout.cancelled`

## 📁 Structure du Projet

```
linekode-PAIEMENT-DIRECT/
├── api/
│   ├── dexpay-checkout.php      # Création de sessions de paiement
│   ├── dexpay-webhook.php       # Réception des notifications
│   └── create-payment-link.php  # Liens de paiement directs
├── css/
│   ├── style.css
│   ├── payment-styles.css
│   └── operator-selection.css
├── js/
│   └── script.js
├── data/                        # Stockage des paiements (gitignored)
├── images/
├── index.html                   # Page d'accueil
├── formations.html              # Liste des formations
├── about.html                   # À propos
├── contact.html                 # Contact
├── inscription.php              # Formulaire d'inscription
├── payment-success.php          # Page de succès
├── payment-cancelled.php        # Page d'annulation
├── config.example.php           # Configuration exemple
├── .env.example                 # Variables d'environnement exemple
├── .gitignore
└── README.md
```

## 🧪 Tests

### Test de l'API

```bash
php test-api-simple.php
```

### Test d'inscription complète

```bash
php test-inscription-production.php
```

Cela créera une session de paiement et vous donnera un lien de test.

## 🔄 Flux de Paiement

1. **Utilisateur** remplit le formulaire d'inscription
2. **Système** crée une session de paiement via `api/dexpay-checkout.php`
3. **Redirection** vers DexpayAfrica pour le paiement
4. **Utilisateur** choisit son mode de paiement (Wave, Orange Money, etc.)
5. **Paiement** effectué
6. **Redirection** selon le résultat :
   - ✅ Succès → `payment-success.php`
   - ❌ Échec → `payment-cancelled.php`
7. **Webhook** notification envoyée à `api/dexpay-webhook.php`
8. **Confirmation** de l'inscription

## 🔒 Sécurité

- ✅ Clés API stockées dans `.env` (non versionnées)
- ✅ Validation des données côté serveur
- ✅ Protection CSRF (à implémenter si nécessaire)
- ✅ Webhooks sécurisés
- ✅ HTTPS requis en production

## 📝 Variables d'Environnement

| Variable | Description | Exemple |
|----------|-------------|---------|
| `DEXPAY_API_KEY` | Clé publique DexpayAfrica | `pk_live_xxx` |
| `DEXPAY_SECRET_KEY` | Clé secrète DexpayAfrica | `sk_live_xxx` |
| `SITE_URL` | URL du site | `https://linekode.com` |
| `INSCRIPTION_AMOUNT` | Montant de l'inscription | `50000` |
| `COUNTRY_ISO` | Code pays | `SN` |
| `CURRENCY` | Devise | `XOF` |

## 🚀 Déploiement

### Sur un serveur web

1. Uploadez tous les fichiers sur votre serveur
2. Configurez `.env` avec vos vraies clés
3. Assurez-vous que PHP et cURL sont installés
4. Configurez le webhook sur DexpayAfrica
5. Testez avec un paiement réel

### Vérifications avant mise en production

- [ ] Clés API de production configurées
- [ ] Webhook configuré sur DexpayAfrica
- [ ] HTTPS activé
- [ ] Permissions des dossiers correctes
- [ ] Test de paiement réel effectué
- [ ] Pages de succès/échec personnalisées
- [ ] Emails de confirmation configurés (optionnel)

## 📊 Monitoring

Les paiements sont enregistrés dans `data/payments.json` (créé automatiquement).

Pour consulter les logs :
- Logs PHP : vérifiez votre `error_log`
- Logs webhook : `data/payments.json`

## 🐛 Dépannage

### Erreur 422 - Validation Failed

Vérifiez que tous les champs requis sont présents dans le payload :
- `amount` (pas `item_price`)
- `countryISO`
- `webhook_url`
- `success_url`
- `failure_url`

### Webhook non reçu

1. Vérifiez la configuration sur https://portal.dexpay.africa/webhooks
2. Assurez-vous que l'URL est accessible publiquement
3. Vérifiez les logs du serveur

### Paiement non enregistré

1. Vérifiez que le dossier `data/` existe et est accessible en écriture
2. Consultez les logs dans `api/dexpay-webhook.php`

## 📞 Support

- **DexpayAfrica** : support@dexpay.africa
- **Documentation** : https://docs.dexpay.africa
- **Portail** : https://portal.dexpay.africa

## 📄 Licence

Ce projet est la propriété de Linekode Sénégal.

## 👥 Auteur

**Linekode Sénégal**
- Email : linekodesn@gmail.com
- Téléphone : +221 71 117 93 93
- Localisation : Saint-Louis, Sénégal

---

**Version** : 1.0  
**Dernière mise à jour** : Mars 2026  
**Statut** : ✅ Production Ready

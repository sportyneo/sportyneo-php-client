# Documentation API SportyNeo

Version 2.0 — Mai 2026

## Introduction

Cette API REST permet de gérer les entités, clubs, clients, commandes, sessions de paiement, attestations de ventes et statistiques du système. Elle utilise une authentification par **token Bearer** avec un identifiant d'entité personnalisé.

**URL de base :** `https://<domaine>/api/v1`

> Tous les montants sont exprimés en **centimes d'euros** (integer). Exemple : `15000` = 150,00 €.

---

## Table des matières

| Fichier | Description |
|---------|-------------|
| [basic-usage.md](basic-usage.md) | Authentification, en-têtes, pagination, erreurs |
| [configuration.md](configuration.md) | Énumérations et endpoint de configuration |
| [entities.md](entities.md) | Gestion des entités |
| [shops.md](shops.md) | Gestion des shops (clubs) |
| [psp.md](psp.md) | Onboarding PSP Ryft (KYB, documents, RIB) |
| [customers.md](customers.md) | Gestion des clients |
| [orders.md](orders.md) | Gestion des commandes |
| [payments.md](payments.md) | Sessions de paiement |
| [invitations.md](invitations.md) | Invitations utilisateurs |
| [statistics.md](statistics.md) | Statistiques globales et par shop |
| [cumulus.md](cumulus.md) | Virements hebdomadaires (cumulus) |
| [sales-attests.md](sales-attests.md) | Attestations de ventes |
| [users.md](users.md) | Gestion des utilisateurs |

---

## Authentification

L'API utilise des **tokens Bearer** (OAuth2-style). Le token est obtenu une fois lors de la connexion et inclus dans toutes les requêtes suivantes.

### Étape 1 — Obtenir un token

```bash
curl -X POST https://api.sportyneo.com/api/v1/auth/token \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"email": "partenaire@exemple.com", "password": "MonSecret123"}'
```

Réponse :

```json
{
  "token": "1|abc123def456...",
  "entities": [
    { "id": 1, "name": "Mon entité" }
  ]
}
```

Le token est valide **30 jours**. Conservez-le côté serveur — ne l'exposez pas côté client.

### Étape 2 — Utiliser le token

Incluez le token dans toutes les requêtes suivantes :

```bash
curl -X GET https://api.sportyneo.com/api/v1/shops \
  -H 'Authorization: Bearer 1|abc123def456...' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Accept: application/json'
```

### Processus de vérification

1. Présence et validité de l'en-tête `Authorization` avec le préfixe `Bearer`
2. Vérification du token en base (table `personal_access_tokens`)
3. Vérification de l'expiration du token
4. Résolution de l'entité via `Sportyneo-Entity-Id` (ou première entité rattachée si absent)
5. Vérification que le compte n'est pas révoqué

### Révocation du token

```bash
curl -X DELETE https://api.sportyneo.com/api/v1/auth/token \
  -H 'Authorization: Bearer 1|abc123def456...'
```

### Codes d'erreur d'authentification

| Code | Erreur | Description |
|------|--------|-------------|
| `401` | `missing_authorization_header` | Header `Authorization` absent |
| `401` | `missing_bearer_token` | Préfixe `Bearer` manquant |
| `401` | `invalid_token` | Token inconnu ou utilisateur révoqué |
| `401` | `token_expired` | Token expiré (re-appeler `POST /auth/token`) |
| `401` | `invalid_entity_id` | Entité inconnue ou non rattachée au compte |

En cas d'échec, le serveur retourne :

```
HTTP/1.1 401 Unauthorized
WWW-Authenticate: Bearer realm="API"
```

### Utilisation via le SDK PHP

L'authentification est gérée **automatiquement** par le SDK. Le constructeur appelle `POST /auth/token` et renouvelle le token si nécessaire :

```php
use Sportyneo\SDK\Client\Client;

$client = new Client(
    email: 'partenaire@exemple.com',
    password: 'MonSecret123',
    entityId: 1,
    baseUrl: 'https://api.sportyneo.com'
);

// Toutes les requêtes suivantes utilisent automatiquement le Bearer token
$shops = $client->shops->list();
```

---

## En-têtes HTTP requis

| Header | Obligatoire | Description | Exemple |
|--------|:-----------:|-------------|---------|
| `Authorization` | ✅ | Token Bearer | `Bearer 1|abc123...` |
| `Sportyneo-Entity-Id` | ✅* | Identifiant numérique de votre entité | `1` |
| `Content-Type` | ✅ (POST/PUT) | Type de contenu | `application/json` |
| `Accept` | Recommandé | Format de réponse souhaité | `application/json` |
| `Sportyneo-Cache` | ❌ | Contrôle du cache (`1` = activé, vide = désactivé) | `1` |

> *Si `Sportyneo-Entity-Id` est absent, l'API utilise la première entité rattachée au compte.

> **Cache :** Certaines réponses sont mises en cache côté serveur (30 min à 1 h). Pour forcer le rafraîchissement, envoyez `Sportyneo-Cache:` (vide) ou omettez l'en-tête.

---

## Pagination

Les endpoints de liste utilisent la pagination Laravel.

### Paramètres

| Paramètre | Description |
|-----------|-------------|
| `page` | Numéro de page (défaut : 1) |
| `per_page` | Éléments par page (défaut : 15, max : 100) |

### Structure de réponse paginée

```json
{
  "data": [],
  "links": {
    "first": "https://api.sportyneo.com/api/v1/resource?page=1",
    "last": "https://api.sportyneo.com/api/v1/resource?page=5",
    "prev": null,
    "next": "https://api.sportyneo.com/api/v1/resource?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

---

## Codes de réponse HTTP

| Code | Signification | Description |
|:----:|---------------|-------------|
| `200` | OK | Requête traitée avec succès |
| `201` | Created | Ressource créée avec succès |
| `401` | Unauthorized | Token invalide, expiré ou absent |
| `403` | Forbidden | Accès refusé (permissions insuffisantes) |
| `404` | Not Found | Ressource introuvable ou accès non autorisé |
| `422` | Unprocessable Entity | Erreurs de validation |
| `429` | Too Many Requests | Trop de tentatives de connexion |

### Erreurs de validation (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["Le champ email est obligatoire."],
    "amount": ["Le champ amount doit être un entier."]
  }
}
```

---

## Notes importantes

- **Montants** : Tous les montants sont en centimes (ex : `15000` = 150,00 €)
- **Dates** : Format ISO 8601 (`YYYY-MM-DD` ou `YYYY-MM-DDTHH:MM:SS.000000Z`)
- **Pagination** : Disponible sur tous les endpoints de liste
- **Cache** : Contrôlable via l'en-tête `Sportyneo-Cache`
- **Token** : Durée de vie 30 jours — à renouveler via `POST /auth/token`

---

*Documentation mise à jour le 2026-05-13*

# Documentation API SportyNeo

Version 1.0 — Février 2026

## Introduction

Cette API REST permet de gérer les entités, clubs, clients, commandes, sessions de paiement, attestations de ventes et statistiques du système. Elle utilise une authentification HTTP Basic Auth avec un identifiant d'entité personnalisé.

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

L'API utilise **HTTP Basic Authentication** (RFC 7617) avec un header supplémentaire pour identifier l'entité.

### Identifiants

Lors de la création de votre compte partenaire, vous recevrez :

- Un **email** de connexion (identifiant)
- Un **mot de passe** (secret client)

### Header Authorization

```
Authorization: Basic base64(email:motdepasse)
```

### Exemple

Pour `partenaire@exemple.com` / `MonSecret123` :

```bash
# base64("partenaire@exemple.com:MonSecret123") = cGFydGVuYWlyZUBleGVtcGxlLmNvbTpNb25TZWNyZXQxMjM=

curl -X GET https://api.sportyneo.com/api/v1/shops \
  -H 'Authorization: Basic cGFydGVuYWlyZUBleGVtcGxlLmNvbTpNb25TZWNyZXQxMjM=' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Accept: application/json'
```

### Processus de vérification

1. Présence et validité de l'en-tête `Authorization` avec le préfixe `Basic`
2. Décodage Base64 et extraction de l'email et du mot de passe
3. Vérification de la présence de l'en-tête `Sportyneo-Entity-Id`
4. Recherche de l'utilisateur par email et vérification de son appartenance à l'entité
5. Vérification du mot de passe (hachage bcrypt)
6. Vérification que le compte n'est pas révoqué

### Codes d'erreur d'authentification

| Code | Erreur | Description |
|------|--------|-------------|
| `401` | `missing_basic_header` | Header `Authorization` manquant ou sans préfixe `Basic` |
| `401` | `invalid_basic_format` | Contenu Base64 invalide ou sans séparateur `:` |
| `401` | `missing_entity_id` | Header `Sportyneo-Entity-Id` manquant |
| `401` | `invalid_credentials` | Email/mot de passe incorrect ou compte révoqué |

En cas d'échec, le serveur retourne :

```
HTTP/1.1 401 Unauthorized
WWW-Authenticate: Basic realm="API", charset="UTF-8"
```

---

## En-têtes HTTP requis

| Header | Obligatoire | Description | Exemple |
|--------|:-----------:|-------------|---------|
| `Authorization` | ✅ | Identifiants Basic Auth en Base64 | `Basic cGFydC4uLg==` |
| `Sportyneo-Entity-Id` | ✅ | Identifiant numérique de votre entité | `1` |
| `Content-Type` | ✅ (POST/PUT) | Type de contenu | `application/json` |
| `Accept` | Recommandé | Format de réponse souhaité | `application/json` |
| `Sportyneo-Cache` | ❌ | Contrôle du cache (`1` = activé, vide = désactivé) | `1` |

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
| `401` | Unauthorized | Authentification invalide ou absente |
| `403` | Forbidden | Accès refusé (permissions insuffisantes) |
| `404` | Not Found | Ressource introuvable ou accès non autorisé |
| `422` | Unprocessable Entity | Erreurs de validation |

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

---

*Documentation mise à jour le 2026-02-10*

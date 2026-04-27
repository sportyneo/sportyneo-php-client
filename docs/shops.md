# Shops (Clubs)

> **Pré-requis :** [Authentification](basic-usage.md#authentification)

Un shop représente un club ou un point de vente rattaché à une entité.

---

## GET /v1/shops

Liste les shops de votre entité.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `external_id` | integer | ❌ | Filtrer par identifiant externe |
| `start_date` | date | ❌ | Début de période pour les statistiques agrégées (YYYY-MM-DD) |
| `end_date` | date | ❌ | Fin de période pour les statistiques agrégées (YYYY-MM-DD) |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "entity_id": 1,
      "external_id": 5001,
      "title": "Club Sportif de Paris",
      "phone": "01 23 45 67 89",
      "email": "contact@club-paris.fr",
      "website": "https://club-paris.fr",
      "category": "Football",
      "street_name": "12 rue du Stade",
      "street_extended": null,
      "postal_code": "75001",
      "city": "Paris",
      "iban": "FR76XXXXXXXXXXXXXXXXXXXX",
      "created_at": "2026-01-15T10:00:00.000000Z",
      "updated_at": "2026-01-15T10:00:00.000000Z"
    }
  ]
}
```

> La réponse est mise en cache pendant **1 heure**. Utilisez `Sportyneo-Cache:` (vide) pour forcer le rafraîchissement.

---

## GET /v1/shops/{id}

Récupère un shop spécifique. Le shop doit appartenir à votre entité.

---

## POST /v1/shops

Crée un nouveau shop rattaché à votre entité.

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `external_id` | integer | ✅ | Votre identifiant interne (unique) |
| `title` | string (max 255) | ❌ | Nom du club |
| `phone` | string (max 255) | ❌ | Numéro de téléphone |
| `email` | email (max 255) | ❌ | Adresse email |
| `website` | url (max 255) | ❌ | Site web |
| `category` | string (max 255) | ❌ | Catégorie / discipline sportive |
| `street_name` | string (max 255) | ❌ | Adresse (rue) |
| `street_extended` | string (max 255) | ❌ | Complément d'adresse |
| `postal_code` | string (max 255) | ❌ | Code postal |
| `city` | string (max 255) | ❌ | Ville |

### Exemple

```bash
curl -X POST https://api.sportyneo.com/api/v1/shops \
  -H 'Authorization: Basic cGFydC4uLg==' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Content-Type: application/json' \
  -d '{
    "external_id": 5002,
    "title": "FC Nantes",
    "phone": "+33240000000",
    "email": "contact@fcnantes.fr",
    "website": "https://www.fcnantes.fr",
    "category": "Football",
    "street_name": "Route de Saint-Joseph",
    "postal_code": "44300",
    "city": "Nantes"
  }'
```

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "entity_id": 1,
  "external_id": 5002,
  "title": "FC Nantes",
  "phone": "+33240000000",
  "email": "contact@fcnantes.fr",
  "website": "https://www.fcnantes.fr",
  "category": "Football",
  "street_name": "Route de Saint-Joseph",
  "street_extended": null,
  "postal_code": "44300",
  "city": "Nantes",
  "iban": null,
  "created_at": "2026-01-20T14:30:00.000000Z",
  "updated_at": "2026-01-20T14:30:00.000000Z"
}
```

---

## Onboarding PSP (Ryft)

Chaque shop peut être associé à un sub-account Ryft pour recevoir des virements. Le shop expose un tableau `psp_accounts` dans sa réponse GET avec le statut d'onboarding courant.

```json
{
    "id": 3,
    "psp_accounts": [
        {
            "psp": "ryft",
            "account_id": "acct_xxxx",
            "onboarding_status": "verified"
        }
    ]
}
```

> Pour le processus complet d'onboarding, voir [psp.md](psp.md).

# Customers (Clients)

> **Pré-requis :** [Authentification](basic-usage.md#authentification)

Les clients représentent les personnes physiques qui passent des commandes auprès de vos shops.

---

## GET /v1/customers

Liste les clients (paginée).

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `email` | string | ❌ | Recherche exacte par email |
| `external_id` | integer | ❌ | Recherche exacte par ID externe |
| `search_text` | string | ❌ | Recherche textuelle sur prénom ou nom |

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "external_id": 12345,
      "first_name": "Jean",
      "surname": "Dupont",
      "email": "jean.dupont@example.com",
      "phone": "+33612345678",
      "birth_date": "1990-05-15",
      "birth_locality": "Paris",
      "address_street": "10 Rue de la Paix",
      "address_extended": null,
      "address_code": "75002",
      "address_locality": "Paris",
      "address_country": "France",
      "created_at": "2026-01-10T09:00:00.000000Z",
      "updated_at": "2026-01-10T09:00:00.000000Z"
    }
  ],
  "links": {},
  "meta": {}
}
```

---

## GET /v1/customers/{id}

Récupère un client spécifique.

---

## POST /v1/customers

Crée un nouveau client.

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `external_id` | integer \| null | ✅ | Votre identifiant interne (nullable) |
| `email` | email (max 255) | ✅ | Adresse email du client |
| `first_name` | string (max 255) | ❌ | Prénom |
| `surname` | string (max 255) | ❌ | Nom de famille |
| `phone` | string (max 255) | ❌ | Téléphone |
| `birth_date` | date (YYYY-MM-DD) | ❌ | Date de naissance |
| `birth_locality` | string (max 255) | ❌ | Lieu de naissance |
| `birth_code` | string (max 255) | ❌ | Code lieu de naissance |
| `address_street` | string (max 255) | ❌ | Adresse (rue) |
| `address_extended` | string (max 255) | ❌ | Complément d'adresse |
| `address_code` | string (max 255) | ❌ | Code postal |
| `address_locality` | string (max 255) | ❌ | Ville |
| `address_country` | string (max 255) | ❌ | Pays |

### Exemple

```bash
curl -X POST https://api.sportyneo.com/api/v1/customers \
  -H 'Authorization: Basic cGFydC4uLg==' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Content-Type: application/json' \
  -d '{
    "external_id": 12345,
    "first_name": "Marie",
    "surname": "Martin",
    "email": "marie.martin@example.com",
    "phone": "+33612345678",
    "birth_date": "1995-08-20",
    "address_street": "25 Avenue des Champs",
    "address_code": "69000",
    "address_locality": "Lyon",
    "address_country": "France"
  }'
```

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "external_id": 12345,
  "first_name": "Marie",
  "surname": "Martin",
  "email": "marie.martin@example.com",
  "phone": "+33612345678",
  "birth_date": "1995-08-20",
  "address_street": "25 Avenue des Champs",
  "address_code": "69000",
  "address_locality": "Lyon",
  "address_country": "France",
  "created_at": "2026-01-20T15:00:00.000000Z",
  "updated_at": "2026-01-20T15:00:00.000000Z"
}
```

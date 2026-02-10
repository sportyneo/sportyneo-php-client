# Users (Utilisateurs)

> **Pré-requis :** [Authentification](basic-usage.md#authentification)

Gestion des comptes d'accès à l'API. Ces opérations nécessitent des permissions spécifiques.

---

## GET /v1/users

Liste les utilisateurs (paginée).

> Nécessite la permission `canListUser`.

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "entities": [
        { "id": 1, "name": "Fédération Exemple" }
      ],
      "created_at": "2026-01-05T08:00:00.000000Z",
      "updated_at": "2026-01-05T08:00:00.000000Z"
    }
  ],
  "links": {},
  "meta": {}
}
```

---

## GET /v1/users/{id}

Récupère un utilisateur spécifique avec ses entités.

> Nécessite la permission `canShowUser`.

---

## POST /v1/users

Crée un nouvel utilisateur.

> Nécessite la permission `canCreateUser`.

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `name` | string (max 255) | ✅ | Nom de l'utilisateur |
| `email` | email (unique) | ✅ | Adresse email (sert d'identifiant) |
| `password` | string (min 8) | ✅ | Mot de passe |
| `entity_id` | integer | ❌ | Entité à laquelle rattacher l'utilisateur |

### Exemple

```bash
curl -X POST https://api.sportyneo.com/api/v1/users \
  -H 'Authorization: Basic cGFydC4uLg==' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Content-Type: application/json' \
  -d '{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "SecurePass123!",
    "entity_id": 1
  }'
```

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "name": "John Doe",
  "email": "john.doe@example.com",
  "entities": [
    { "id": 1, "name": "Fédération Exemple" }
  ],
  "created_at": "2026-01-20T17:00:00.000000Z",
  "updated_at": "2026-01-20T17:00:00.000000Z"
}
```

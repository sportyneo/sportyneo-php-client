# Entities (Entités)

> **Pré-requis :** [Authentification](basic-usage.md#authentification)

Une entité représente une organisation (fédération, ligue, comité) qui regroupe un ensemble de shops.

---

## GET /v1/entities

Liste toutes les entités auxquelles vous avez accès (paginée).

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Fédération Exemple",
      "payment_config": {
        "workflow": { "1": [1, 4, 12], "5": [], "6": [] },
        "instalment_fees": {},
        "service_fees": {},
        "instalment_free": []
      },
      "created_at": "2026-01-15T10:00:00.000000Z",
      "updated_at": "2026-01-15T10:00:00.000000Z"
    }
  ],
  "links": {},
  "meta": {}
}
```

### Champ `payment_config`

Configuration des passerelles de paiement de l'entité :

| Clé | Description |
|-----|-------------|
| `workflow` | Passerelles activées (clé = PaymentMethod) et échéanciers disponibles (valeur = tableau d'Instalment) |
| `instalment_fees` | Frais par échéancier |
| `service_fees` | Frais de service |
| `instalment_free` | Échéanciers sans frais |

---

## GET /v1/entities/{id}

Récupère une entité spécifique.

> Vous devez être rattaché à l'entité demandée, sinon une erreur 403 sera retournée.

---

## POST /v1/entities

Crée une nouvelle entité.

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `name` | string (max 255) | ✅ | Nom de l'entité |

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "name": "Nouvelle Entité",
  "created_at": "2026-01-20T14:00:00.000000Z",
  "updated_at": "2026-01-20T14:00:00.000000Z"
}
```

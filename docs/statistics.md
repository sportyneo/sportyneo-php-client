# Statistics (Statistiques)

> **Pré-requis :** [Authentification](basic-usage.md#authentification)

---

## Statistiques globales

### GET /v1/statistics/summary

Résumé statistique pour votre entité.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `start_date` | date | ❌ | Date de début (défaut : 12 mois avant) |
| `end_date` | date | ❌ | Date de fin (défaut : aujourd'hui) |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "entity_id": 1,
      "date": "2026-01-15",
      "sales_amount": 450000,
      "sales_count": 150,
      "cb1x_amount": 200000,
      "cb1x_count": 80,
      "cb4x_amount": 150000,
      "cb4x_count": 50,
      "cb10x_amount": 50000,
      "cb10x_count": 15,
      "cb12x_amount": 50000,
      "cb12x_count": 5,
      "insurance_amount": 7500,
      "insurance_count": 15,
      "created_at": "2026-01-16T08:00:00.000000Z",
      "updated_at": "2026-01-16T08:00:00.000000Z"
    }
  ]
}
```

> Mis en cache pendant **30 minutes**.

### GET /v1/statistics/summary/{id}

Récupère une statistique spécifique. Doit appartenir à votre entité.

### GET /v1/statistics/payment-volumes

Volumes de paiement agrégés par type d'échéancier.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `sport` | string | ❌ | Filtrer par discipline sportive |
| `start_date` | date | ❌ | Date de début |
| `end_date` | date | ❌ | Date de fin |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "data": {
    "cb1x": { "amount": 2000000, "count": 800 },
    "cb4x": { "amount": 1500000, "count": 500 },
    "cb10x": { "amount": 500000, "count": 150 },
    "cb12x": { "amount": 500000, "count": 50 }
  }
}
```

---

## Statistiques par shop

### GET /v1/statistics-shops/statsShopsSummary

Statistiques agrégées par shop.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `shop_id` | integer | ❌ | Filtrer par shop |
| `start_date` | date | ❌ | Date de début |
| `end_date` | date | ❌ | Date de fin |

**Champs retournés par shop :**

| Champ | Type | Description |
|-------|------|-------------|
| `shop_id` | integer | Identifiant du shop |
| `sales_amount` | integer | Montant total des ventes (centimes) |
| `sales_count` | integer | Nombre de ventes |
| `cb1x_amount` / `cb1x_count` | integer | Paiements en 1 fois |
| `cb4x_amount` / `cb4x_count` | integer | Paiements en 4 fois |
| `cb10x_amount` / `cb10x_count` | integer | Paiements en 10 fois |
| `cb12x_amount` / `cb12x_count` | integer | Paiements en 12 fois |
| `insurance_amount` / `insurance_count` | integer | Assurances |

> Mis en cache pendant **30 minutes**.

### GET /v1/statistics-shops/statsShopsSummary/daily

Statistiques journalières par shop (mêmes paramètres et champs, avec un champ `date` supplémentaire).

**Réponse (200 OK) :**

```json
{
  "start_date": "2026-01-01",
  "end_date": "2026-01-31",
  "daily_data": []
}
```

---

## Activité des shops

### GET /v1/statistics-shops/active-shops

Shops ayant eu au moins une commande sur la période. Le nombre de shops inactifs = `total_shops - count`.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `start_date` | date | ❌ | Date de début |
| `end_date` | date | ❌ | Date de fin (défaut : aujourd'hui) |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "count": 42,
  "data": [
    { "id": 3, "title": "Club Olympique", "orders_count": 20 }
  ]
}
```

### GET /v1/statistics-shops/churned-shops

Shops ayant eu des commandes sur la même période l'année N-1, mais **aucune** commande cette année.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `start_date` | date | ✅ | Début de la période N (YYYY-MM-DD) |
| `end_date` | date | ✅ | Fin de la période N (YYYY-MM-DD) |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "period_n":  { "start": "2024-01-01", "end": "2024-06-30" },
  "period_n1": { "start": "2023-01-01", "end": "2023-06-30" },
  "count": 3,
  "data": [{ "id": 5, "title": "Club Inactif" }]
}
```

---

## Répartition des shops

### GET /v1/statistics-shops/distribution/by-category

Répartition des shops par catégorie sportive.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `sport` | string | ❌ | Filtrer par sport |
| `region` | string | ❌ | Filtrer par région |
| `department` | string | ❌ | Filtrer par département |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "filters": { "sport": null, "region": null, "department": null },
  "data": [
    { "category": "Football", "count": 150 },
    { "category": "Rugby", "count": 85 }
  ]
}
```

### GET /v1/statistics-shops/distribution/by-region

Répartition géographique des shops (mêmes paramètres).

**Réponse (200 OK) :**

```json
{
  "success": true,
  "filters": { "sport": null, "region": null, "department": null },
  "data": [
    { "region": "Île-de-France", "count": 200 },
    { "region": "Auvergne-Rhône-Alpes", "count": 120 }
  ]
}
```

### GET /v1/statistics-shops/distribution/by-product-type

Volumes de transactions par type de produit (Adhésions, Stages, Boutique, etc.).

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `start_date` | date | ❌ | Date de début |
| `end_date` | date | ❌ | Date de fin (défaut : aujourd'hui) |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "data": [
    { "type": "membership", "amount": 500000, "count": 40 },
    { "type": "training",   "amount": 120000, "count": 8 }
  ]
}
```

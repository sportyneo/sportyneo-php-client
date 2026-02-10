# Sales Attests (Attestations de ventes)

> **Pré-requis :** [Authentification](basic-usage.md#authentification)

Les attestations de ventes sont des documents récapitulatifs générés pour chaque shop, détaillant les ventes d'une période et le montant net à verser au club.

---

## GET /v1/sales-attests/shop/{shop_id}/generate

Génère l'attestation de ventes pour un shop à une date de transfert donnée.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `date` | date (YYYY-MM-DD) | ✅ | Date de transfert |

### Exemple

```bash
curl -X GET "https://api.sportyneo.com/api/v1/sales-attests/shop/1/generate?date=2026-02-09" \
  -H 'Authorization: Basic cGFydC4uLg==' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Accept: application/json'
```

---

## GET /v1/sales-attests/shop/{shop_id}/yearly

Toutes les attestations d'un shop pour une année.

| Paramètre query | Type | Requis | Description |
|-----------------|------|:------:|-------------|
| `year` | integer (2020–2100) | ✅ | Année |

### Exemple

```bash
curl -X GET "https://api.sportyneo.com/api/v1/sales-attests/shop/1/yearly?year=2026" \
  -H 'Authorization: Basic cGFydC4uLg==' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Accept: application/json'
```

**Réponse (200 OK) :**

```json
{
  "year": 2026,
  "shop_id": 1,
  "shop_name": "Club Sportif de Paris",
  "total_weeks": 52,
  "current_week": "2026-02-09",
  "attests": [
    {
      "id": 1,
      "transfer_date": "2026-02-09",
      "transfer_week": 6,
      "sales_period_start": "2026-01-20",
      "sales_period_end": "2026-01-26",
      "orders_count": 12,
      "gross_amount": 180000,
      "product_amount": 170000,
      "insurance_amount": 5000,
      "fees_amount": 3000,
      "discounts_amount": 1000,
      "donations_amount": 500,
      "net_club_amount": 171000,
      "pdf_path": "attests/1/...",
      "download_url": "https://..."
    }
  ]
}
```

### Détail des montants

| Champ | Description |
|-------|-------------|
| `gross_amount` | Montant brut total (centimes) |
| `product_amount` | Montant produits club (centimes) |
| `insurance_amount` | Assurances (centimes) |
| `fees_amount` | Frais bancaires (centimes) |
| `discounts_amount` | Coupons et remises (centimes) |
| `donations_amount` | Donations (centimes) |
| `net_club_amount` | Montant net versé au club (centimes) |

---

## GET /v1/sales-attests/{salesAttest}/download

Télécharge le PDF de l'attestation.

> Cet endpoint utilise une **URL signée** (signature temporaire). L'URL de téléchargement est fournie dans le champ `download_url` de l'attestation. **Aucune authentification Basic n'est requise** pour cet endpoint.

### Exemple

```bash
# L'URL signée est fournie dans download_url, utilisez-la directement
curl -o attestation.pdf "https://api.sportyneo.com/api/v1/sales-attests/1/download?signature=..."
```

# Cumulus (Virements hebdomadaires)

> **Pré-requis :** [Authentification](basic-usage.md#authentification)

Le cumulus représente le détail des virements hebdomadaires (payouts) d'une entité sur une année. Chaque semaine correspond à un lundi (date de virement). Les semaines futures sont ignorées. Les semaines en cours et non encore clôturées sont calculées en temps réel.

> Pas de resource dédiée dans le SDK — utilisez directement `$client->get()`.

---

## GET /v1/cumulus/entity/{entity_id}/yearly

Retourne tous les virements hebdomadaires d'une entité pour une année.

```php
$data = $client->get('/cumulus/entity/1/yearly', ['year' => 2024]);
```

| Paramètre | Type | Requis | Description |
|-----------|------|:------:|-------------|
| `year` | integer | ✅ | Année (2020–2100) |

**Réponse (200 OK) :**

```json
{
    "year": 2024,
    "entity_id": 1,
    "entity_name": "Fédération Sportive",
    "total_weeks": 48,
    "current_week": "2024-12-02",
    "payouts": [
        {
            "transfer_date": "2024-01-08",
            "transfer_week": 2,
            "amount": 125000,
            "is_persisted": true,
            "download_xml_url": "https://api.sportyneo.com/api/v1/payouts/5/download/xml?signature=...",
            "download_xls_url": "https://api.sportyneo.com/api/v1/payouts/5/download/xls?signature=..."
        },
        {
            "transfer_date": "2024-12-02",
            "transfer_week": 49,
            "amount": 98000,
            "is_persisted": false
        }
    ]
}
```

### Champs de la réponse

| Champ | Description |
|-------|-------------|
| `total_weeks` | Nombre de semaines retournées (semaines passées uniquement) |
| `current_week` | Lundi de la semaine en cours |
| `payouts[].transfer_date` | Date du virement (lundi) |
| `payouts[].amount` | Montant du virement en centimes |
| `payouts[].is_persisted` | `true` = virement clôturé et enregistré en base |
| `payouts[].download_xml_url` | URL signée pour télécharger le fichier XML (si `is_persisted`) |
| `payouts[].download_xls_url` | URL signée pour télécharger le fichier Excel (si `is_persisted`) |

---

## Téléchargement des fichiers de virement

Les URLs de téléchargement sont des **URLs signées** — elles expirent rapidement. **Aucune authentification Basic n'est requise.**

### GET /v1/payouts/{payout_id}/download/xml

Télécharge le fichier XML du virement.

```bash
curl -o virement.xml "https://api.sportyneo.com/api/v1/payouts/5/download/xml?signature=..."
```

### GET /v1/payouts/{payout_id}/download/xls

Télécharge le fichier Excel du virement.

```bash
curl -o virement.xlsx "https://api.sportyneo.com/api/v1/payouts/5/download/xls?signature=..."
```

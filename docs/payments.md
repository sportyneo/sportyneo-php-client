# Payments (Sessions de paiement)

> **Pré-requis :** [Authentification](basic-usage.md#authentification) — [Énumérations](configuration.md)

Les sessions de paiement permettent de générer un lien de paiement sécurisé à transmettre à votre client. Ce lien redirige vers la page de paiement SportyNeo où le client pourra choisir son mode de paiement et son échéancier.

---

## POST /v1/payments

Génère un lien de paiement sécurisé.

### Corps de la requête

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `shop_id` | integer | ✅ | Identifiant du shop |
| `customer_id` | integer | ✅ | Identifiant du client |
| `internal_id` | integer | ✅ | Votre identifiant interne de commande |
| `amount` | integer | ✅ | Montant total à payer (centimes) |
| `items` | array | ✅ | Tableau des articles (voir ci-dessous) |
| `return_url` | url | ✅ | URL de retour après paiement réussi |
| `error_url` | url | ✅ | URL de retour après échec |
| `back_url` | url | ✅ | URL de retour si abandon |
| `allowed_institutions` | string[] | ✅ | Institutions de remise autorisées — slugs [DiscountInstitution](configuration.md#institutions-de-remise-discountinstitution) (voir [détails](#institutions-de-remise)) |
| `allowed_payment_methods` | integer[] | ❌ | Modes de paiement autorisés — valeurs [PaymentMethod](configuration.md#modes-de-paiement-paymentmethod) (voir [détails](#modes-de-paiement-autorisés)) |
| `external_id` | string | ❌ | Identifiant externe |

### Structure d'un article (`items[]`)

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `id` | integer | ✅ | Identifiant unique de l'article |
| `name` | string | ✅ | Nom du produit |
| `quantity` | integer | ✅ | Quantité |
| `unit_price` | integer | ✅ | Prix unitaire (centimes) |
| `type` | integer | ❌ | Type (cf. [OrderItemType](configuration.md#types-darticles-orderitemtype)) |
| `category` | integer | ❌ | Catégorie (cf. [OrderItemCategory](configuration.md#catégories-darticles-orderitemcategory)) |
| `vat_rate` | decimal | ❌ | Taux de TVA (ex : 20.00) |
| `start_date` | date | ❌ | Date de début de validité |
| `end_date` | date | ❌ | Date de fin de validité |
| `options` | object | ❌ | Options (ex : `external_id`, `is_insurable`) |
| `metadata` | object | ❌ | Métadonnées du bénéficiaire (prénom, nom, email, etc.) |

### Exemple de requête

```bash
curl -X POST https://api.sportyneo.com/api/v1/payments \
  -H 'Authorization: Basic cGFydC4uLg==' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "shop_id": 1,
    "customer_id": 100,
    "internal_id": 20260001,
    "amount": 15000,
    "items": [
      {
        "id": 1,
        "name": "Licence annuelle - Senior",
        "quantity": 1,
        "unit_price": 15000,
        "type": 1,
        "category": 1,
        "vat_rate": 20.00,
        "start_date": "2026-03-01",
        "end_date": "2027-02-28",
        "options": {
          "external_id": "LIC-2026-001",
          "is_insurable": true
        },
        "metadata": {
          "first_name": "Jean",
          "last_name": "Dupont",
          "birth_date": "1990-05-15",
          "email": "jean.dupont@email.fr",
          "phone": "0612345678"
        }
      }
    ],
    "return_url": "https://monsite.fr/paiement/succes",
    "error_url": "https://monsite.fr/paiement/erreur",
    "back_url": "https://monsite.fr/paiement/annulation"
  }'
```

### Réponse (201 Created)

```json
{
  "success": true,
  "message": "URL de paiement générée avec succès",
  "payment_url": "https://paiement.sportyneo.com/checkout/a1b2c3d4-...",
  "cart_id": 42,
  "token": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "expires_at": "2026-02-11T14:30:00.000000Z"
}
```

---

## Institutions de remise

Le champ `allowed_institutions` contrôle quelles institutions de remise (ex. Pass Sport) le client peut utiliser sur la page de paiement. Chaque valeur est le **slug** d'une institution (enum [`DiscountInstitution`](configuration.md#institutions-de-remise-discountinstitution)). Passez un tableau vide `[]` si aucune institution n'est applicable.

| Slug | Nom | Portée |
|------|-----|--------|
| `pass_commune` | Pass Commune | Local |
| `pass_region` | Pass Région | Régional |
| `pass_region_rhone_alpes` | Pass Région Rhône-Alpes | Régional |
| `pass_sport` | Pass Sport | National |

```php
// Exemple : autoriser Pass Sport et Pass Région
'allowed_institutions' => ['pass_sport', 'pass_region'],

// Exemple : aucune institution de remise
'allowed_institutions' => [],
```

---

## Modes de paiement autorisés

Le champ optionnel `allowed_payment_methods` restreint les modes de paiement proposés au client sur la page de paiement. Chaque valeur est l'**identifiant numérique** d'un mode de paiement (enum [`PaymentMethod`](configuration.md#modes-de-paiement-paymentmethod)). Omettez le champ (ou passez un tableau vide `[]`) pour laisser disponibles tous les modes activés sur votre entité.

| Valeur | Code API | Nom | Type |
|:------:|----------|-----|------|
| `1` | ALMAPAY | Alma | En ligne |
| `2` | FLOAPAY | Floa | En ligne |
| `3` | STRIPE | Stripe | En ligne |
| `4` | CB | Carte Bancaire | En ligne |
| `5` | CHECK | Chèque | Physique |
| `6` | COD | Espèces | Physique |
| `7` | RYFT | Ryft | En ligne |
| `8` | BANK_TRANSFER | Virement bancaire | Physique |

```php
// Exemple : autoriser uniquement la carte bancaire et Alma
'allowed_payment_methods' => [4, 1],

// Exemple : aucune restriction (tous les modes activés sur l'entité)
'allowed_payment_methods' => [],
```

> Seuls les modes effectivement activés sur votre entité (cf. [`workflow`](entities.md)) sont proposés ; `allowed_payment_methods` agit comme un filtre supplémentaire au sein de cet ensemble.

---

## Flux de paiement

```
1. POST /v1/payments           → Vous créez une session de paiement
2. Réponse : payment_url       → Vous recevez un lien de paiement + token
3. Redirection client          → Vous redirigez le client vers payment_url
4. Page SportyNeo              → Le client choisit mode de paiement + échéancier
5a. Succès                     → Le client est redirigé vers return_url
5b. Échec                      → Le client est redirigé vers error_url
5c. Abandon                    → Le client est redirigé vers back_url
6. Commande créée              → La commande est créée automatiquement côté SportyNeo
7. GET /v1/orders              → Vous vérifiez le statut de la commande
```

> Le lien de paiement expire après **24 heures**. Passé ce délai, il faudra générer une nouvelle session.

---

## Passerelles disponibles

Le choix de la passerelle est automatique selon la configuration de votre entité.

| Passerelle | ID | Échéanciers supportés |
|------------|:--:|----------------------|
| Alma | 1 | CB1XC, CB3X, CB4X, CB10X, CB12X |
| Floa | 2 | CB1XC, CB1XD, CB3X, CB4X, CB10X |
| Stripe | 3 | En cours d'implémentation |
| Chèque | 5 | Paiement direct (hors ligne) |
| Espèces | 6 | Paiement direct (hors ligne) |

### Calcul des frais

Lors de la création d'une session, les frais sont calculés automatiquement :

| Élément | Description |
|---------|-------------|
| `serviceFees` | Frais fixes liés au service SportyNeo |
| `financialFees` | Frais liés à l'échéancier choisi |
| `totalFees` | `serviceFees` + `financialFees` |
| `schedule` | Tableau des montants de chaque échéance (centimes) |

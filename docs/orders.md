# Orders — Documentation API

> **Authentification :** HTTP Basic Auth + `Sportyneo-Entity-Id` (voir [basic-usage.md](basic-usage.md#authentification))
>
> **Montants :** Tous en centimes d'euros (integer). `15000` = 150,00 €.

---

## 1. Liste des commandes (avec filtres)

```
GET /api/v1/orders
```

Récupère la liste paginée des commandes de l'entité, triées par date de création décroissante.

### Headers

```
Authorization: Basic base64(email:motdepasse)
Sportyneo-Entity-Id: 1
Accept: application/json
```

### Query Parameters

| Paramètre | Type | Requis | Description |
|-----------|------|:------:|-------------|
| `internal_id` | string | ❌ | ID interne de la commande |
| `external_id` | string | ❌ | ID externe de la commande |
| `date_from` | date (YYYY-MM-DD) | ❌ | Date de début (incluse) |
| `date_to` | date (YYYY-MM-DD) | ❌ | Date de fin (incluse) |
| `status` | integer | ❌ | Statut (cf. enum OrderStatus) |
| `customer_id` | integer | ❌ | ID du client |
| `shop_id` | integer | ❌ | ID du shop (doit appartenir à l'entité) |
| `instalment` | integer | ❌ | Type d'échéancier (cf. enum Instalment) |
| `with_insurance` | boolean | ❌ | Filtrer les commandes avec assurance |
| `search_text` | string | ❌ | Recherche sur prénom/nom du client |
| `per_page` | integer (1–100) | ❌ | Éléments par page (défaut : 15) |

### Énumérations de référence

#### OrderStatus (Backed Enum: int)

| Valeur | Code | Description |
|:------:|------|-------------|
| 1 | CART | Panier en cours |
| 2 | PENDING | En attente de paiement |
| 3 | PAID | Payée |
| 4 | CANCELLED | Annulée |
| 5 | FAILED | Échouée |
| 6 | REIMBURSED | Remboursée |
| 7 | ISSUED | Émise |
| 8 | SAVED | Sauvegardée |

#### Instalment (Backed Enum: int)

| Valeur | Code | Description | Nb paiements |
|:------:|------|-------------|:------------:|
| 1 | CB1XC | Paiement unique par carte | 1 |
| 2 | CB1XD | Paiement unique par prélèvement | 1 |
| 3 | CB3X | Paiement en 3 fois | 3 |
| 4 | CB4X | Paiement en 4 fois | 4 |
| 10 | CB10X | Paiement en 10 fois | 10 |
| 12 | CB12X | Paiement en 12 fois | 12 |

#### PaymentMethod (Backed Enum: int)

| Valeur | Code API | Nom | Type |
|:------:|----------|-----|------|
| 1 | ALMAPAY | Alma | En ligne |
| 2 | FLOAPAY | Floa | En ligne |
| 3 | STRIPE | Stripe | En ligne |
| 4 | CB | Carte Bancaire | En ligne |
| 5 | CHECK | Chèque | Physique |
| 6 | COD | Espèces | Physique |
| 7 | RYFT | Ryft | En ligne |
| 8 | BANK_TRANSFER | Virement bancaire | Physique |

#### OrderDeliveryStatus (Backed Enum: int)

| Valeur | Code | Description |
|:------:|------|-------------|
| 1 | PENDING | En attente d'expédition |
| 2 | SHIPPED | Expédiée |
| 3 | DELIVERED | Livrée |

### Exemples de requêtes

```bash
# Commandes payées du dernier mois avec assurance
GET /api/v1/orders?status=3&date_from=2026-01-01&with_insurance=true

# Commandes en 10x pour un client spécifique
GET /api/v1/orders?customer_id=123&instalment=10

# Commandes d'un shop avec pagination
GET /api/v1/orders?shop_id=456&per_page=25

# Recherche par external_id
GET /api/v1/orders?external_id=EXT-98765

# Recherche par nom du client
GET /api/v1/orders?search_text=Dupont
```

### Réponse (200 OK)

```json
{
  "data": [
    {
      "id": 1,
      "shop_id": 456,
      "customer_id": 123,
      "internal_id": "ORD-12345",
      "external_id": "EXT-98765",
      "reference": "REF-2026-001",
      "status": 3,
      "payment_method": 3,
      "instalment": 10,
      "subtotal": 50000,
      "amount": 52500,
      "amount_club": 52500,
      "customer": {
        "id": 123,
        "first_name": "Jean",
        "surname": "Dupont",
        "email": "jean.dupont@example.com"
      },
      "shop": {
        "id": 456,
        "title": "Club Sportif de Paris",
        "entity_id": 1
      },
      "paid_date": "2026-01-20T14:30:00.000000Z",
      "created_at": "2026-01-20T10:30:00.000000Z",
      "updated_at": "2026-01-20T14:30:00.000000Z"
    }
  ],
  "links": {
    "first": "https://api.sportyneo.com/api/v1/orders?page=1",
    "last": "https://api.sportyneo.com/api/v1/orders?page=10",
    "prev": null,
    "next": "https://api.sportyneo.com/api/v1/orders?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

---

## 2. Détail d'une commande

```
GET /api/v1/orders/{id}
```

Récupère les détails complets d'une commande avec les informations du client.

### Path Parameters

| Paramètre | Type | Requis | Description |
|-----------|------|:------:|-------------|
| `id` | integer | ✅ | ID de la commande |

### Réponse (200 OK)

```json
{
  "id": 1,
  "shop_id": 456,
  "customer_id": 123,
  "cart_id": 789,
  "internal_id": "ORD-12345",
  "external_id": "EXT-98765",
  "reference": "REF-2026-001",
  "status": 3,
  "payment_method": 3,
  "instalment": 10,
  "due_date": null,
  "paid_date": "2026-01-20T14:30:00.000000Z",
  "payment_received_date": "2026-01-20T14:35:00.000000Z",
  "comment": "Commande validée",
  "subtotal": 50000,
  "refund": 0,
  "coupons": 500,
  "discounts": 1000,
  "donations": 200,
  "insurances": 1500,
  "deliveries": 800,
  "fees": 500,
  "fees_services": 0,
  "amount": 52500,
  "refund_club": 0,
  "amount_club": 52500,
  "ip_address": "192.168.1.1",
  "payment_url": "https://payment.gateway.com/checkout/abc123",
  "return_url": "https://shop.example.com/order/success",
  "error_url": "https://shop.example.com/order/error",
  "back_url": "https://shop.example.com/cart",
  "session_id": "sess_abc123xyz",
  "channel": "web",
  "is_archived": false,
  "delivery": "home",
  "relay_id": null,
  "delivery_status": 2,
  "shipping_first_name": "Jean",
  "shipping_surname": "Dupont",
  "shipping_street_name": "12 rue de la République",
  "shipping_street_extended": "Apt 3B",
  "shipping_postal_code": "75001",
  "shipping_locality": "Paris",
  "shipping_country_code": "FR",
  "shipping_company": null,
  "migrated_date": null,
  "customer": {
    "id": 123,
    "first_name": "Jean",
    "surname": "Dupont",
    "email": "jean.dupont@example.com",
    "phone": "+33612345678"
  },
  "created_at": "2026-01-20T10:30:00.000000Z",
  "updated_at": "2026-01-20T14:30:00.000000Z"
}
```

### Erreurs

**404 Not Found** — Commande introuvable ou n'appartient pas à votre entité.

---

## 3. Créer une commande

```
POST /api/v1/orders
```

Crée une nouvelle commande.

### Champs obligatoires

| Champ | Type | Description |
|-------|------|-------------|
| `shop_id` | integer | ID du shop (doit appartenir à l'entité) |
| `customer_id` | integer | ID du client |
| `internal_id` | integer | Votre identifiant interne |
| `reference` | string (max 40) | Référence unique de la commande |
| `status` | integer | Statut initial (cf. enum OrderStatus) |
| `payment_method` | integer | Mode de paiement (cf. enum PaymentMethod) |
| `instalment` | integer | Échéancier (cf. enum Instalment) |
| `subtotal` | integer | Sous-total (centimes) |
| `refund` | integer | Remboursement (centimes) |
| `coupons` | integer | Coupons (centimes) |
| `discounts` | integer | Remises (centimes) |
| `donations` | integer | Dons (centimes) |
| `insurances` | integer | Assurances (centimes) |
| `deliveries` | integer | Frais de livraison (centimes) |
| `fees` | integer | Frais bancaires (centimes) |
| `fees_services` | integer | Frais de service (centimes) |
| `amount` | integer | Montant total TTC (centimes) |
| `refund_club` | integer | Remboursement club (centimes) |
| `amount_club` | integer | Montant net pour le club (centimes) |

### Champs optionnels

| Champ | Type | Description |
|-------|------|-------------|
| `external_id` | string | Identifiant externe |
| `due_date` | date | Date d'échéance |
| `paid_date` | date | Date de paiement |
| `created_at` | date | Date de création (défaut : maintenant) |
| `comment` | string | Commentaire |
| `ip_address` | string (max 50) | Adresse IP du client |
| `payment_url` | url (max 360) | URL de paiement |
| `return_url` | url (max 255) | URL de retour succès |
| `error_url` | url (max 255) | URL de retour erreur |
| `back_url` | url (max 255) | URL de retour abandon |
| `session_id` | string (max 255) | ID de session |
| `channel` | string (max 255) | Canal de vente |
| `is_archived` | boolean | Archivé ou non |
| `delivery` | string (max 255) | Mode de livraison |
| `relay_id` | integer | ID du point relais |
| `migrated_date` | date | Date de migration |
| `delivery_status` | integer | Statut livraison (cf. enum OrderDeliveryStatus) |
| `shipping_first_name` | string (max 255) | Prénom livraison |
| `shipping_surname` | string (max 255) | Nom livraison |
| `shipping_street_name` | string (max 255) | Rue livraison |
| `shipping_street_extended` | string (max 255) | Complément adresse livraison |
| `shipping_postal_code` | string (max 32) | Code postal livraison |
| `shipping_locality` | string (max 255) | Ville livraison |
| `shipping_country_code` | string (max 255) | Code pays livraison |
| `shipping_company` | string (max 255) | Société de livraison |
| `payment_received_date` | date | Date de réception paiement |
| `items` | array | Tableau d'articles (cf. ci-dessous) |

### Structure d'un article (`items[]`)

| Champ | Type | Description |
|-------|------|-------------|
| `id` | integer | Identifiant de l'article |
| `name` | string | Nom du produit |
| `quantity` | integer | Quantité |
| `unit_price` | integer | Prix unitaire (centimes) |
| `type` | integer | Type (cf. enum OrderItemType) |
| `category` | integer | Catégorie (cf. enum OrderItemCategory) |
| `vat_rate` | decimal | Taux de TVA (ex : 20.00) |
| `start_date` | date | Date de début de validité |
| `end_date` | date | Date de fin de validité |
| `options` | object | Options supplémentaires |
| `metadata` | object | Métadonnées du bénéficiaire |

#### OrderItemType (Backed Enum: int)

| Valeur | Slug | Description | Montant positif |
|:------:|------|-------------|:---------------:|
| 1 | product | Produit | ✅ |
| 2 | insurance | Assurance | ✅ |
| 3 | fees | Frais | ✅ |
| 4 | financial_fees | Frais financiers | ✅ |
| 5 | discount | Réduction | ❌ (déduction) |
| 6 | coupon | Coupon | ❌ (déduction) |
| 7 | donation | Don | ✅ |
| 8 | eticket | E-Ticket | ✅ |
| 9 | delivery | Livraison | ✅ |

#### OrderItemCategory (Backed Enum: int)

| Valeur | Description | Éligible aux remises |
|:------:|-------------|:--------------------:|
| 1 | Adhésions / Licences | ✅ |
| 2 | Stages | ✅ |
| 3 | Autres | ❌ |
| 4 | Boutique | ❌ |
| 5 | Planning | ❌ |
| 6 | Merchandising | ❌ |

### Exemple de requête

```bash
curl -X POST https://api.sportyneo.com/api/v1/orders \
  -H 'Authorization: Basic cGFydC4uLg==' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "shop_id": 1,
    "customer_id": 100,
    "internal_id": 1001,
    "reference": "REF-20260120-001",
    "status": 3,
    "payment_method": 4,
    "instalment": 1,
    "paid_date": "2026-01-20",
    "subtotal": 14000,
    "refund": 0,
    "coupons": 500,
    "discounts": 0,
    "donations": 0,
    "insurances": 500,
    "deliveries": 1000,
    "fees": 200,
    "fees_services": 300,
    "amount": 15000,
    "refund_club": 0,
    "amount_club": 14500,
    "channel": "online",
    "items": [
      {
        "id": 1,
        "name": "Licence annuelle - Senior",
        "quantity": 1,
        "unit_price": 14000,
        "type": 1,
        "category": 1,
        "vat_rate": 20.00,
        "start_date": "2026-03-01",
        "end_date": "2027-02-28"
      }
    ]
  }'
```

### Réponse (201 Created)

```json
{
  "id": 42,
  "shop_id": 1,
  "customer_id": 100,
  "internal_id": 1001,
  "reference": "REF-20260120-001",
  "status": 3,
  "payment_method": 4,
  "instalment": 1,
  "subtotal": 14000,
  "amount": 15000,
  "amount_club": 14500,
  "paid_date": "2026-01-20T00:00:00.000000Z",
  "created_at": "2026-01-20T16:00:00.000000Z",
  "updated_at": "2026-01-20T16:00:00.000000Z"
}
```

---

## 4. Modifier une commande

```
PUT /api/v1/orders/{id}
```

Met à jour une commande existante.

### Path Parameters

| Paramètre | Type | Requis | Description |
|-----------|------|:------:|-------------|
| `id` | integer | ✅ | ID de la commande |

### Corps de la requête

Mêmes champs que la création. La `reference` doit rester unique (à l'exclusion de la commande en cours de modification).

> **Attention :** Les articles existants sont **supprimés et remplacés** par ceux fournis dans la requête. Si vous ne fournissez pas `items`, les articles existants sont conservés.

### Exemple

```bash
curl -X PUT https://api.sportyneo.com/api/v1/orders/42 \
  -H 'Authorization: Basic cGFydC4uLg==' \
  -H 'Sportyneo-Entity-Id: 1' \
  -H 'Content-Type: application/json' \
  -d '{
    "shop_id": 1,
    "customer_id": 100,
    "internal_id": 1001,
    "reference": "REF-20260120-001",
    "status": 3,
    "payment_method": 4,
    "instalment": 1,
    "subtotal": 14000,
    "refund": 0,
    "coupons": 500,
    "discounts": 0,
    "donations": 0,
    "insurances": 500,
    "deliveries": 1000,
    "fees": 200,
    "fees_services": 300,
    "amount": 15000,
    "refund_club": 0,
    "amount_club": 14500,
    "delivery_status": 2,
    "comment": "Paiement validé, commande expédiée"
  }'
```

### Réponse (200 OK)

```json
{
  "id": 42,
  "shop_id": 1,
  "customer_id": 100,
  "status": 3,
  "delivery_status": 2,
  "comment": "Paiement validé, commande expédiée",
  "amount": 15000,
  "updated_at": "2026-01-21T09:00:00.000000Z"
}
```

---

## Workflow typique

```
1. Créer un client         POST /api/v1/customers
2. Créer une commande      POST /api/v1/orders  (status: 2 = PENDING)
   — OU —
   Créer une session       POST /api/v1/payments  (crée la commande automatiquement)
3. Vérifier le statut      GET  /api/v1/orders?external_id=...
4. Mettre à jour           PUT  /api/v1/orders/{id}  (delivery_status: 2 = SHIPPED)
```

---

## Codes d'erreur

| Code | Description |
|:----:|-------------|
| `200` | Requête réussie |
| `201` | Ressource créée avec succès |
| `401` | Authentification invalide ou manquante |
| `404` | Commande introuvable ou accès non autorisé |
| `422` | Erreurs de validation (détails par champ dans `errors`) |

---

## Notes importantes

- **Montants** : Tous en centimes (ex : `15000` = 150,00 €)
- **Dates** : Format ISO 8601 (`YYYY-MM-DD` ou `YYYY-MM-DDTHH:MM:SS.000000Z`)
- **Filtres** : Cumulables sur l'endpoint `GET /api/v1/orders`
- **Pagination** : 15 éléments par défaut, 100 maximum
- **Sécurité** : Les commandes sont automatiquement filtrées par entité et par accès aux shops

---

*Documentation mise à jour le 2026-02-10*

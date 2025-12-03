# Orders API Documentation

## Endpoints

### 1. Liste des commandes (avec filtres)
```
GET /api/orders
```

Récupère la liste des commandes de l'entité avec filtres optionnels et pagination.

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Query Parameters

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `internal_id` | string | Non | ID interne de la commande |
| `external_id` | string | Non | ID externe de la commande |
| `date_from` | date | Non | Date de début (format: Y-m-d) |
| `date_to` | date | Non | Date de fin (format: Y-m-d) |
| `status` | integer | Non | Statut de la commande (voir enum OrderStatus) |
| `customer_id` | integer | Non | ID du client (doit exister dans `customers`) |
| `shop_id` | integer | Non | ID de la boutique (doit appartenir à l'entité) |
| `instalment` | integer | Non | Type de paiement échelonné (voir enum Instalment) |
| `with_insurance` | boolean | Non | Filtre présence assurance (`true`/`false`/`1`/`0`) |
| `per_page` | integer | Non | Nombre d'éléments par page (défaut: 15, max: 100) |

#### Enum OrderStatus (Backed Enum: int)

| Valeur | Label | Description |
|--------|-------|-------------|
| 1 | Cart | Panier |
| 2 | Pending | En attente de paiement |
| 3 | Paid | Payée |
| 4 | Cancelled | Annulée |
| 5 | Failed | Échouée |
| 6 | Reimbursed | Remboursée |
| 7 | Issued | Émise |
| 8 | Saved | Sauvegardée |

#### Enum Instalment (Backed Enum: int)

| Valeur | Label | Display Name |
|--------|-------|--------------|
| 1 | CB1XC | Paiement unique par carte |
| 2 | CB1XD | Paiement unique par prélèvement |
| 3 | CB3X | Paiement en 3 fois |
| 4 | CB4X | Paiement en 4 fois |
| 10 | CB10X | Paiement en 10 fois |
| 12 | CB12X | Paiement en 12 fois |

#### Exemples de requêtes

**Commandes payées du dernier mois avec assurance :**
```
GET /api/orders?status=3&date_from=2024-11-01&with_insurance=true
```

**Commandes en 10x pour un client spécifique :**
```
GET /api/orders?customer_id=123&instalment=10
```

**Commandes d'une boutique avec pagination :**
```
GET /api/orders?shop_id=456&per_page=25
```

**Recherche par external_id :**
```
GET /api/orders?external_id=EXT-98765
```

**Commandes échouées sans assurance :**
```
GET /api/orders?status=5&with_insurance=false
```

#### Réponse (200 OK)
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "internal_id": "ORD-12345",
      "external_id": "EXT-98765",
      "reference": "REF-2024-001",
      "status": 3,
      "status_label": "Paid",
      "payment_method": 3,
      "payment_method_label": "Stripe",
      "instalment": 10,
      "instalment_label": "CB10X",
      "subtotal": 50000,
      "amount": 52500,
      "customer": {
        "id": 123,
        "first_name": "Jean",
        "last_name": "Dupont",
        "email": "jean.dupont@example.com"
      },
      "shop": {
        "id": 456,
        "name": "Ma Boutique Sport",
        "entity_id": 1
      },
      "delivery_status": 1,
      "paid_date": "2024-12-01T14:30:00.000000Z",
      "created_at": "2024-12-01T10:30:00.000000Z",
      "updated_at": "2024-12-01T14:30:00.000000Z"
    }
  ],
  "first_page_url": "http://api.example.com/orders?page=1",
  "from": 1,
  "last_page": 10,
  "last_page_url": "http://api.example.com/orders?page=10",
  "links": [...],
  "next_page_url": "http://api.example.com/orders?page=2",
  "path": "http://api.example.com/orders",
  "per_page": 15,
  "prev_page_url": null,
  "to": 15,
  "total": 150
}
```

---

### 2. Détail d'une commande
```
GET /api/orders/{id}
```

Récupère les détails d'une commande spécifique.

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Path Parameters

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `id` | integer | Oui | ID de la commande |

#### Réponse (200 OK)
```json
{
  "id": 1,
  "shop_id": 456,
  "customer_id": 123,
  "cart_id": 789,
  "internal_id": "ORD-12345",
  "external_id": "EXT-98765",
  "reference": "REF-2024-001",
  "status": 3,
  "status_label": "Paid",
  "payment_method": 3,
  "payment_method_label": "Stripe",
  "instalment": 10,
  "instalment_label": "CB10X",
  "due_date": null,
  "paid_date": "2024-12-01T14:30:00.000000Z",
  "payment_received_date": "2024-12-01T14:35:00.000000Z",
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
  "delivery_status_label": "Shipped",
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
    "last_name": "Dupont",
    "email": "jean.dupont@example.com",
    "phone": "+33612345678"
  },
  "created_at": "2024-12-01T10:30:00.000000Z",
  "updated_at": "2024-12-01T14:30:00.000000Z"
}
```

#### Erreurs

**404 Not Found** - Commande non trouvée ou n'appartient pas à l'entité
```json
{
  "error": "Not found"
}
```

---

### 3. Créer une commande
```
POST /api/orders
```

Crée une nouvelle commande.

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Body Parameters

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `shop_id` | integer | Oui | ID de la boutique (doit appartenir à l'entité) |
| `customer_id` | integer | Oui | ID du client |
| `cart_id` | integer | Non | ID du panier associé |
| `internal_id` | string | Non | ID interne |
| `external_id` | string | Non | ID externe |
| `reference` | string | Non | Référence de la commande |
| `status` | integer | Oui | Statut (voir OrderStatus) |
| `payment_method` | integer | Non | Méthode de paiement (voir PaymentMethod) |
| `instalment` | integer | Non | Échelonnement (voir Instalment) |
| `subtotal` | integer | Oui | Sous-total en centimes |
| `amount` | integer | Oui | Montant total en centimes |
| `comment` | string | Non | Commentaire |
| `ip_address` | string | Non | Adresse IP du client |
| `channel` | string | Non | Canal de vente (web, mobile, etc.) |
| `shipping_*` | string | Non | Informations de livraison |

#### Enum PaymentMethod (Backed Enum: int)

| Valeur | Label | Display Name |
|--------|-------|--------------|
| 1 | ALMAPAY | Alma |
| 2 | FLOAPAY | Floa |
| 3 | STRIPE | Stripe |
| 4 | CB | Carte Bancaire |
| 5 | CHECK | Chèque |
| 6 | COD | Espèces |

#### Enum OrderDeliveryStatus (Backed Enum: int)

| Valeur | Label | Description |
|--------|-------|-------------|
| 1 | Pending | En attente |
| 2 | Shipped | Expédiée |
| 3 | Delivered | Livrée |

#### Exemple de requête
```json
{
  "shop_id": 456,
  "customer_id": 123,
  "cart_id": 789,
  "status": 2,
  "payment_method": 3,
  "instalment": 10,
  "subtotal": 50000,
  "coupons": 500,
  "discounts": 1000,
  "insurances": 1500,
  "deliveries": 800,
  "fees": 500,
  "amount": 52500,
  "channel": "web",
  "shipping_first_name": "Jean",
  "shipping_surname": "Dupont",
  "shipping_street_name": "12 rue de la République",
  "shipping_postal_code": "75001",
  "shipping_locality": "Paris",
  "shipping_country_code": "FR"
}
```

#### Réponse (201 Created)
```json
{
  "id": 1,
  "shop_id": 456,
  "customer_id": 123,
  "cart_id": 789,
  "status": 2,
  "status_label": "Pending",
  "payment_method": 3,
  "payment_method_label": "Stripe",
  "instalment": 10,
  "subtotal": 50000,
  "amount": 52500,
  "created_at": "2024-12-01T10:30:00.000000Z",
  "updated_at": "2024-12-01T10:30:00.000000Z"
}
```

---

### 4. Modifier une commande
```
PUT/PATCH /api/orders/{id}
```

Met à jour une commande existante.

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Path Parameters

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `id` | integer | Oui | ID de la commande |

#### Body Parameters

Mêmes paramètres que la création, tous optionnels pour une mise à jour partielle.

#### Exemple de requête
```json
{
  "status": 3,
  "paid_date": "2024-12-01T14:30:00Z",
  "delivery_status": 2,
  "comment": "Paiement validé, commande en préparation"
}
```

#### Réponse (200 OK)
```json
{
  "id": 1,
  "status": 3,
  "status_label": "Paid",
  "paid_date": "2024-12-01T14:30:00.000000Z",
  "delivery_status": 2,
  "delivery_status_label": "Shipped",
  "comment": "Paiement validé, commande en préparation",
  "updated_at": "2024-12-01T14:35:00.000000Z"
}
```

---

## OrderItems (Items de commande)

Chaque commande peut contenir plusieurs items avec différents types.

### Enum OrderItemType (Backed Enum: int)

| Valeur | Slug | Label | Description |
|--------|------|-------|-------------|
| 1 | product | Produit | Article standard |
| 2 | insurance | Assurance | Assurance (déclenche création abonnement Neat) |
| 3 | fees | Frais | Frais divers |
| 4 | financial_fees | Frais financiers | Frais financiers |
| 5 | discount | Réduction | Réduction appliquée |
| 6 | coupon | Coupon | Code promo |
| 7 | donation | Don | Don optionnel |
| 8 | eticket | E-Ticket | Billet électronique |
| 9 | delivery | Livraison | Frais de livraison |

### Enum OrderItemCategory (Backed Enum: int)

| Valeur | Label | Discountable |
|--------|-------|--------------|
| 1 | Adhésions | Oui |
| 2 | Stages | Oui |
| 3 | Autres | Non |
| 4 | Boutique | Non |
| 5 | Planning | Non |
| 6 | Merchandising | Non |

---

## Notes importantes

### Sécurité et autorisations

- Toutes les requêtes nécessitent une authentification Bearer token
- Les commandes sont automatiquement filtrées par `entity_id` de l'utilisateur
- Le paramètre `shop_id` est validé pour appartenir à l'entité
- Les montants sont stockés en **centimes** (ex: 100€ = 10000)

### Comportements spéciaux

- **Insurance items**: Lors du paiement, déclenche automatiquement la création d'un abonnement via NeatService
- **Filtres cumulables**: Tous les filtres de l'endpoint `index` peuvent être combinés
- **Dates**: Les filtres `date_from` et `date_to` utilisent `created_at` avec `whereDate`
- **Pagination**: Par défaut 15 éléments, maximum 100 par page

### Codes d'erreur

| Code | Description |
|------|-------------|
| 200 | OK - Succès |
| 201 | Created - Ressource créée |
| 404 | Not Found - Ressource introuvable ou accès interdit |
| 422 | Unprocessable Entity - Erreurs de validation |
| 401 | Unauthorized - Token invalide ou manquant |

---

## Exemples complets

### Workflow typique de commande

**1. Créer une commande en attente**
```bash
POST /api/orders
{
  "shop_id": 456,
  "customer_id": 123,
  "status": 2,  # Pending
  "payment_method": 3,  # Stripe
  "instalment": 10,  # CB10X
  "subtotal": 50000,
  "amount": 52500
}
```

**2. Rechercher la commande**
```bash
GET /api/orders?external_id=EXT-98765
```

**3. Marquer comme payée**
```bash
PATCH /api/orders/1
{
  "status": 3,  # Paid
  "paid_date": "2024-12-01T14:30:00Z"
}
```

**4. Mettre à jour le statut de livraison**
```bash
PATCH /api/orders/1
{
  "delivery_status": 2  # Shipped
}
```
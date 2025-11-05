# Documentation API

Version 1.0

## Introduction

Cette API REST permet de gérer les entités, clubs, clients, commandes et statistiques du système. Elle utilise une authentification HTTP Basic Auth avec un identifiant d'entité personnalisé.

**URL de base :** `https://api.sportyneo.com/api`

---

## Table des matières

- [Authentification](#authentification)
- [Pagination](#pagination)
- [Entities (Entités)](#entities-entités)
- [Shops (Clubs)](#shops-clubs)
- [Customers (Clients)](#customers-clients)
- [Orders (Commandes)](#orders-commandes)
- [Users (Utilisateurs)](#users-utilisateurs)
- [Statistics (Statistiques)](#statistics-statistiques)
- [Shop Stats (Statistiques Clubs)](#shop-stats-statistiques-clubs)
- [Codes de réponse HTTP](#codes-de-réponse-http)

---

## Authentification

L'API utilise **HTTP Basic Authentication** avec un header supplémentaire pour identifier l'entité.

### Headers requis

| Header | Description |
|--------|-------------|
| `Authorization` | Basic Auth : `Basic base64(email:password)` |
| `Sportyneo-Entity-Id` | ID de l'entité (requis) |

### Exemple de requête

```bash
curl -X GET https://api.sportyneo.com/api/customers \
  -H 'Authorization: Basic dXNlckBleGFtcGxlLmNvbTpwYXNzd29yZA==' \
  -H 'Sportyneo-Entity-Id: 123'
```

### Codes d'erreur d'authentification

| Code | Description |
|------|-------------|
| `401` | `missing_basic_header` - Header Authorization manquant |
| `401` | `invalid_basic_format` - Format Basic Auth invalide |
| `401` | `missing_entity_id` - Header Sportyneo-Entity-Id manquant |
| `401` | `invalid_credentials` - Email ou mot de passe incorrect |

---

## Pagination

Les endpoints qui retournent des listes utilisent la pagination Laravel. Chaque réponse contient les métadonnées de pagination.

### Paramètres de pagination

| Paramètre | Description |
|-----------|-------------|
| `page` | Numéro de page (défaut: 1) |
| `per_page` | Éléments par page (défaut: 15, max: 200) |

### Structure de réponse paginée

```json
{
  "data": [],
  "current_page": 1,
  "last_page": 5,
  "per_page": 15,
  "total": 75,
  "from": 1,
  "to": 15,
  "path": "https://api.sportyneo.com/api/customers",
  "next_page_url": "https://api.sportyneo.com/api/customers?page=2",
  "prev_page_url": null
}
```

---

## Entities (Entités)

Gestion des entités (organisations, fédérations, etc.)

### GET /entities

Liste toutes les entités (paginée).

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Fédération Française de Football",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "current_page": 1,
  "last_page": 1
}
```

### GET /entities/{id}

Récupère une entité spécifique.

**Réponse (200 OK) :**

```json
{
  "id": 1,
  "name": "Fédération Française de Football",
  "created_at": "2024-01-15T10:30:00.000000Z",
  "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### POST /entities

Crée une nouvelle entité.

**Paramètres :**

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `name` | string | ✅ | Nom de l'entité (max 255 caractères) |

**Corps de la requête :**

```json
{
  "name": "Nouvelle Entité"
}
```

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "name": "Nouvelle Entité",
  "created_at": "2024-01-20T14:00:00.000000Z",
  "updated_at": "2024-01-20T14:00:00.000000Z"
}
```

---

## Shops (Clubs)

Gestion des clubs sportifs.

### GET /shops

Liste tous les clubs (paginé).

**Paramètres query :**

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `external_id` | integer | ❌ | Filtrer par ID externe |

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "external_id": 5001,
      "entity_id": 1,
      "entity": { "id": 1, "name": "FFF" },
      "title": "AS Monaco",
      "phone": "+33612345678",
      "email": "contact@asmonaco.fr",
      "website": "https://www.asmonaco.fr",
      "category": "Football",
      "street_name": "7 Avenue des Castelans",
      "street_extended": null,
      "postal_code": "98000",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ]
}
```

### GET /shops/{id}

Récupère un club spécifique avec ses relations.

**Réponse (200 OK) :**

```json
{
  "id": 1,
  "external_id": 5001,
  "entity_id": 1,
  "entity": { "id": 1, "name": "FFF" },
  "title": "AS Monaco",
  "phone": "+33612345678",
  "email": "contact@asmonaco.fr",
  "website": "https://www.asmonaco.fr",
  "category": "Football",
  "street_name": "7 Avenue des Castelans",
  "street_extended": null,
  "postal_code": "98000",
  "created_at": "2024-01-15T10:30:00.000000Z",
  "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### POST /shops

Crée un nouveau club.

**Paramètres :**

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `entity_id` | integer | ✅ | ID de l'entité (doit exister) |
| `external_id` | integer | ✅ | ID externe (unique) |
| `title` | string | ❌ | Nom du club (max 255 caractères) |
| `phone` | string | ❌ | Téléphone (max 255 caractères) |
| `email` | string | ❌ | Email (format email, max 255) |
| `website` | string | ❌ | Site web (format URL, max 255) |
| `category` | string | ❌ | Catégorie/Sport (max 255 caractères) |
| `street_name` | string | ❌ | Nom de rue (max 255 caractères) |
| `street_extended` | string | ❌ | Complément d'adresse (max 255) |
| `postal_code` | string | ❌ | Code postal (max 255 caractères) |

**Corps de la requête :**

```json
{
  "entity_id": 1,
  "external_id": 5002,
  "title": "FC Nantes",
  "phone": "+33240000000",
  "email": "contact@fcnantes.fr",
  "website": "https://www.fcnantes.fr",
  "category": "Football",
  "street_name": "Route de Saint-Joseph",
  "postal_code": "44300"
}
```

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "external_id": 5002,
  "entity_id": 1,
  "entity": { "id": 1, "name": "FFF" },
  "title": "FC Nantes",
  "phone": "+33240000000",
  "email": "contact@fcnantes.fr",
  "website": "https://www.fcnantes.fr",
  "category": "Football",
  "street_name": "Route de Saint-Joseph",
  "street_extended": null,
  "postal_code": "44300",
  "created_at": "2024-01-20T14:30:00.000000Z",
  "updated_at": "2024-01-20T14:30:00.000000Z"
}
```

---

## Customers (Clients)

Gestion des clients.

### GET /customers

Liste tous les clients (paginée).

**Paramètres query :**

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `mail` | string | ❌ | Filtrer par email |
| `external_id` | integer | ❌ | Filtrer par ID externe |

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "first_name": "Jean",
      "surname": "Dupont",
      "mail": "jean.dupont@example.com",
      "birth_date": "1990-05-15",
      "address_street": "10 Rue de la Paix",
      "address_code": "75002",
      "address_locality": "Paris",
      "address_country": "France",
      "created_at": "2024-01-10T09:00:00.000000Z",
      "updated_at": "2024-01-10T09:00:00.000000Z"
    }
  ]
}
```

### GET /customers/{id}

Récupère un client spécifique.

**Réponse (200 OK) :**

```json
{
  "id": 1,
  "first_name": "Jean",
  "surname": "Dupont",
  "mail": "jean.dupont@example.com",
  "birth_date": "1990-05-15",
  "address_street": "10 Rue de la Paix",
  "address_code": "75002",
  "address_locality": "Paris",
  "address_country": "France",
  "created_at": "2024-01-10T09:00:00.000000Z",
  "updated_at": "2024-01-10T09:00:00.000000Z"
}
```

### POST /customers

Crée un nouveau client.

**Règle importante :** Au moins `external_id` OU `mail` est requis.

**Paramètres :**

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `external_id` | integer | 🔶 | ID externe (requis si mail absent) |
| `mail` | string | 🔶 | Email (requis si external_id absent, unique, max 255) |
| `first_name` | string | ❌ | Prénom (max 255 caractères) |
| `surname` | string | ❌ | Nom de famille (max 255 caractères) |
| `phone` | string | ❌ | Téléphone (max 255 caractères) |
| `birth_date` | date | ❌ | Date de naissance (format: YYYY-MM-DD) |
| `birth_locality` | string | ❌ | Lieu de naissance (max 255) |
| `address_street` | string | ❌ | Rue (max 255 caractères) |
| `address_extended` | string | ❌ | Complément d'adresse (max 255) |
| `address_code` | string | ❌ | Code postal (max 255 caractères) |
| `address_locality` | string | ❌ | Ville (max 255 caractères) |
| `address_country` | string | ❌ | Pays (max 255 caractères) |

**Corps de la requête :**

```json
{
  "external_id": 12345,
  "first_name": "Marie",
  "surname": "Martin",
  "mail": "marie.martin@example.com",
  "phone": "+33612345678",
  "birth_date": "1995-08-20",
  "address_street": "25 Avenue des Champs",
  "address_code": "69000",
  "address_locality": "Lyon",
  "address_country": "France"
}
```

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "first_name": "Marie",
  "surname": "Martin",
  "mail": "marie.martin@example.com",
  "birth_date": "1995-08-20",
  "address_street": "25 Avenue des Champs",
  "address_code": "69000",
  "address_locality": "Lyon",
  "address_country": "France",
  "created_at": "2024-01-20T15:00:00.000000Z",
  "updated_at": "2024-01-20T15:00:00.000000Z"
}
```

---

## Orders (Commandes)

Gestion des commandes.

### GET /orders

Liste toutes les commandes avec shop et customer (paginée).

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "shop_id": 1,
      "shop": { "id": 1, "title": "AS Monaco" },
      "customer_id": 1,
      "customer": { "id": 1, "mail": "jean.dupont@example.com" },
      "status": "paid",
      "paid_date": "2024-01-20",
      "payment_method": "card",
      "instalment": "1x",
      "total_amount": 15000,
      "refund_amount": 0,
      "coupon_amount": 500,
      "discount_amount": 0,
      "deliveries_amount": 1000,
      "fees_amount": 200,
      "service_amount": 300,
      "insurance_amount": 500,
      "insurance_count": 1,
      "created_at": "2024-01-20T14:30:00.000000Z",
      "updated_at": "2024-01-20T14:30:00.000000Z"
    }
  ]
}
```

### GET /orders/{id}

Récupère une commande spécifique avec ses relations.

**Réponse (200 OK) :**

```json
{
  "id": 1,
  "shop_id": 1,
  "shop": { "id": 1, "title": "AS Monaco" },
  "customer_id": 1,
  "customer": { "id": 1, "mail": "jean.dupont@example.com" },
  "status": "paid",
  "paid_date": "2024-01-20",
  "payment_method": "card",
  "instalment": "1x",
  "total_amount": 15000,
  "refund_amount": 0,
  "coupon_amount": 500,
  "discount_amount": 0,
  "deliveries_amount": 1000,
  "fees_amount": 200,
  "service_amount": 300,
  "insurance_amount": 500,
  "insurance_count": 1,
  "created_at": "2024-01-20T14:30:00.000000Z",
  "updated_at": "2024-01-20T14:30:00.000000Z"
}
```

### POST /orders

Crée une nouvelle commande.

**Paramètres :**

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `shop_id` | integer | ✅ | ID du club (doit exister) |
| `customer_id` | integer | ✅ | ID du client (doit exister) |
| `internal_id` | integer | ✅ | ID interne |
| `external_id` | mixed | ✅ | ID externe |
| `reference` | string | ✅ | Référence unique (max 40, unique) |
| `status` | string | ✅ | Statut de la commande (max 255) |
| `payment_method` | string | ❌ | Méthode de paiement (max 255) |
| `instalment` | string | ❌ | Type d'échelonnement (max 255) |
| `due_date` | date | ❌ | Date d'échéance (format: YYYY-MM-DD) |
| `paid_date` | date | ❌ | Date de paiement (format: YYYY-MM-DD) |
| `comment` | string | ❌ | Commentaire |
| `subtotal` | integer | ✅ | Sous-total (en centimes) |
| `refund` | integer | ✅ | Montant remboursé (en centimes) |
| `coupons` | integer | ✅ | Montant coupons (en centimes) |
| `discounts` | integer | ✅ | Montant remises (en centimes) |
| `donations` | integer | ✅ | Montant dons (en centimes) |
| `insurances` | integer | ✅ | Montant assurances (en centimes) |
| `deliveries` | integer | ✅ | Montant livraisons (en centimes) |
| `fees` | integer | ✅ | Montant frais (en centimes) |
| `fees_services` | integer | ✅ | Montant frais de service (en centimes) |
| `amount` | integer | ✅ | Montant total (en centimes) |
| `refund_club` | integer | ✅ | Montant remboursé au club (en centimes) |
| `amount_club` | integer | ✅ | Montant dû au club (en centimes) |
| `ip_address` | string | ❌ | Adresse IP (max 50) |
| `payment_url` | string | ❌ | URL de paiement (format URL, max 360) |
| `return_url` | string | ❌ | URL de retour (format URL, max 255) |
| `error_url` | string | ❌ | URL d'erreur (format URL, max 255) |
| `back_url` | string | ❌ | URL de retour arrière (format URL, max 255) |
| `session_id` | string | ❌ | ID de session (max 255) |
| `channel` | string | ❌ | Canal de vente (max 255) |
| `is_archived` | boolean | ❌ | Archivé ou non |
| `delivery` | string | ❌ | Type de livraison (max 255) |
| `relay_id` | string | ❌ | ID du point relais (max 255) |
| `migrated_date` | date | ❌ | Date de migration |
| `delivery_status` | string | ❌ | Statut de livraison (max 255) |
| `shipping_first_name` | string | ❌ | Prénom de livraison (max 255) |
| `shipping_surname` | string | ❌ | Nom de livraison (max 255) |
| `shipping_street_name` | string | ❌ | Rue de livraison (max 255) |
| `shipping_street_extended` | string | ❌ | Complément adresse livraison (max 255) |
| `shipping_postal_code` | string | ❌ | Code postal livraison (max 32) |
| `shipping_locality` | string | ❌ | Ville de livraison (max 255) |
| `shipping_country_code` | string | ❌ | Code pays livraison (max 2) |
| `shipping_company` | string | ❌ | Société de livraison (max 255) |
| `payment_received_date` | date | ❌ | Date de réception paiement |

**Corps de la requête :**

```json
{
  "shop_id": 1,
  "customer_id": 1,
  "internal_id": 1001,
  "external_id": "EXT-2024-001",
  "reference": "REF-20240120-001",
  "status": "paid",
  "payment_method": "card",
  "instalment": "1x",
  "paid_date": "2024-01-20",
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
  "ip_address": "192.168.1.1",
  "channel": "online"
}
```

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "shop_id": 1,
  "shop": { "id": 1, "title": "AS Monaco" },
  "customer_id": 1,
  "customer": { "id": 1, "mail": "jean.dupont@example.com" },
  "status": "paid",
  "paid_date": "2024-01-20",
  "payment_method": "card",
  "instalment": "1x",
  "total_amount": 15000,
  "insurance_amount": 500,
  "created_at": "2024-01-20T16:00:00.000000Z",
  "updated_at": "2024-01-20T16:00:00.000000Z"
}
```

---

## Users (Utilisateurs)

Gestion des utilisateurs de l'API.

### GET /users

Liste tous les utilisateurs avec leurs entités (paginée).

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "entity_ids": [1, 2],
      "entities": [
        { "id": 1, "name": "FFF" },
        { "id": 2, "name": "FFR" }
      ],
      "created_at": "2024-01-05T08:00:00.000000Z",
      "updated_at": "2024-01-05T08:00:00.000000Z"
    }
  ]
}
```

### GET /users/{id}

Récupère un utilisateur spécifique.

**Réponse (200 OK) :**

```json
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@example.com",
  "entity_ids": [1, 2],
  "entities": [
    { "id": 1, "name": "FFF" },
    { "id": 2, "name": "FFR" }
  ],
  "created_at": "2024-01-05T08:00:00.000000Z",
  "updated_at": "2024-01-05T08:00:00.000000Z"
}
```

### POST /users

Crée un nouvel utilisateur.

**Paramètres :**

| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `name` | string | ✅ | Nom de l'utilisateur (max 255) |
| `email` | string | ✅ | Email (format email, unique, max 255) |
| `password` | string | ✅ | Mot de passe (min 8 caractères) |
| `entity_id` | integer | ❌ | ID de l'entité (doit exister) |

**Corps de la requête :**

```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "password": "SecurePass123!",
  "entity_id": 1
}
```

**Réponse (201 Created) :**

```json
{
  "id": 2,
  "name": "John Doe",
  "email": "john.doe@example.com",
  "entity_ids": [1],
  "entities": [
    { "id": 1, "name": "FFF" }
  ],
  "created_at": "2024-01-20T17:00:00.000000Z",
  "updated_at": "2024-01-20T17:00:00.000000Z"
}
```

---

## Statistics (Statistiques)

Endpoints avancés pour les statistiques et analyses.

### GET /statistics

Liste toutes les statistiques (paginée).

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "entity_id": 1,
      "date": "2024-01-15",
      "entity": { "id": 1, "name": "FFF" },
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
      "created_at": "2024-01-16T08:00:00.000000Z",
      "updated_at": "2024-01-16T08:00:00.000000Z"
    }
  ]
}
```

### GET /statistics/{id}

Récupère une statistique spécifique.

### GET /statistics/shops

Liste des clubs filtrés par sport, région, département.

**Paramètres query :**

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `sport` | string | ❌ | Sport (ex: Football, Rugby) |
| `region` | string | ❌ | Région (ex: Auvergne-Rhône-Alpes) |
| `department` | string | ❌ | Département (ex: 69) |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "filters": {
    "sport": "Football",
    "region": null,
    "department": null
  },
  "count": 150,
  "data": [
    {
      "id": 1,
      "title": "AS Monaco",
      "category": "Football"
    }
  ]
}
```

### GET /statistics/inactive-shops

Clubs sans commande depuis X jours (seuils d'alertes).

**Paramètres query :**

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `days` | integer | ❌ | Nombre de jours sans commande (défaut: 30) |

**Réponse (200 OK) :**

```json
{
  "success": true,
  "threshold_days": 30,
  "count": 25,
  "data": [
    {
      "id": 5,
      "title": "Club Inactif",
      "last_order_date": "2023-12-15"
    }
  ]
}
```

### GET /statistics/payment-volumes

Volumes de paiement agrégés par type (1x, 4x, 10x, 12x).

**Paramètres query :**

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `sport` | string | ❌ | Sport (ex: Football) |
| `start_date` | date | ❌ | Date de début (format: YYYY-MM-DD) |
| `end_date` | date | ❌ | Date de fin (format: YYYY-MM-DD) |

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

## Shop Stats (Statistiques Clubs)

Statistiques détaillées par club.

### GET /shop-stats

Liste toutes les statistiques de clubs (paginée).

**Réponse (200 OK) :**

```json
{
  "data": [
    {
      "id": 1,
      "shop_id": 1,
      "date": "2024-01-15",
      "shop": { "id": 1, "title": "AS Monaco" },
      "sales_amount": 45000,
      "sales_count": 15,
      "cb1x_amount": 20000,
      "cb1x_count": 8,
      "cb4x_amount": 15000,
      "cb4x_count": 5,
      "cb10x_amount": 5000,
      "cb10x_count": 1,
      "cb12x_amount": 5000,
      "cb12x_count": 1,
      "insurance_amount": 750,
      "insurance_count": 3,
      "created_at": "2024-01-16T08:00:00.000000Z",
      "updated_at": "2024-01-16T08:00:00.000000Z"
    }
  ]
}
```

### GET /shop-stats/{id}

Récupère une statistique d'un club spécifique.

### GET /shop-stats/summary

Résumé agrégé des statistiques d'un club sur une période.

**Paramètres query :**

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `shop_id` | integer | ✅ | ID du club |
| `from` | date | ❌ | Date de début (format: YYYY-MM-DD, défaut: 2024-01-01) |
| `to` | date | ❌ | Date de fin (format: YYYY-MM-DD, défaut: aujourd'hui) |

**Réponse (200 OK) :**

```json
{
  "shop_id": 1,
  "from": "2024-01-01",
  "to": "2024-12-31",
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
  "insurance_count": 15
}
```

### GET /shop-stats/summary-live

Statistiques en temps réel d'un club calculées depuis les commandes payées.

**Paramètres : identiques à `/shop-stats/summary`**

### GET /shop-stats/summary-live-all

Statistiques en temps réel pour tous les clubs, triées par montant de ventes décroissant.

**Paramètres query :**

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `from` | date | ❌ | Date de début (défaut: 2024-01-01) |
| `to` | date | ❌ | Date de fin (défaut: aujourd'hui) |

**Réponse (200 OK) :**

```json
[
  {
    "shop_id": 1,
    "id": 1,
    "entity_id": 1,
    "external_id": 5001,
    "title": "AS Monaco",
    "email": "contact@asmonaco.fr",
    "category": "Football",
    "sales_amount": 450000,
    "sales_count": 150,
    "cb1x_amount": 200000,
    "cb1x_count": 80,
    "cb4x_amount": 150000,
    "cb4x_count": 50,
    "cb10x_amount": 50000,
    "cb10x_count": 15,
    "cb12x_amount": 50000,
    "cb12x_count": 5
  }
]
```

### GET /shop-stats/summary-live-paginated

Version paginée de `summary-live-all`.

**Paramètres query supplémentaires :**

| Paramètre | Type | Requis | Description |
|-----------|------|--------|-------------|
| `per_page` | integer | ❌ | Éléments par page (min: 1, max: 200, défaut: 15) |

---

## Codes de réponse HTTP

| Code | Description |
|------|-------------|
| `200` | OK - Requête réussie |
| `201` | Created - Ressource créée avec succès |
| `400` | Bad Request - Paramètres invalides |
| `401` | Unauthorized - Authentification requise ou invalide |
| `404` | Not Found - Ressource non trouvée |
| `422` | Unprocessable Entity - Erreurs de validation |
| `500` | Internal Server Error - Erreur serveur |

---

## Notes importantes

- **Montants** : Tous les montants sont en centimes (ex: 15000 = 150,00€)
- **Dates** : Format ISO 8601 (YYYY-MM-DD ou YYYY-MM-DDTHH:MM:SS)
- **Pagination** : Toujours disponible sur les endpoints de liste
- **Relations** : Utilisez les paramètres de relations pour charger les données associées

---

*Documentation générée le 2024-01-20*
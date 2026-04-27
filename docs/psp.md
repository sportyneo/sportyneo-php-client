# PSP / Onboarding Ryft

> **Pré-requis :** [Authentification](basic-usage.md#authentification) — Le shop doit exister avant de démarrer l'onboarding.

L'onboarding PSP permet à un shop de recevoir des virements via Ryft (le prestataire de paiement de Sportyneo). Le processus complet se déroule en plusieurs étapes : création du sub-account, upload de documents KYB, soumission des personnes (bénéficiaires effectifs / représentants légaux), puis ajout d'un RIB.

---

## Statuts d'onboarding (PspOnboardingStatus)

| Valeur | Description |
|--------|-------------|
| `pending` | Compte créé, onboarding pas encore commencé |
| `action_required` | Documents ou informations manquants |
| `verified` | Onboarding complet — le shop peut recevoir des virements |
| `rejected` | Rejeté par Ryft |
| `suspended` | Suspendu |

Seul le statut `verified` permet l'encaissement réel.

---

## Étape 1 — Créer le sub-account Ryft

### POST /v1/shops/{shop_id}/psp/create

```php
$client->shops->createPsp($shopId, [
    'entity_type' => 'Business',
    'terms_of_service' => true,
    'business' => [
        'name' => 'Club Olympique SAS',
        'type' => 'Association',
        'registration_number' => '12345678900010',
        'contact_email' => 'contact@club.fr',
        'registered_address' => [
            'line_one' => '5 avenue du Stade',
            'city' => 'Paris',
            'country' => 'FR',
            'postal_code' => '75001',
        ],
    ],
]);
```

### Corps de la requête

**`entity_type`** : `Business` (entreprise/association) ou `Individual` (personne physique).

#### Si `entity_type = Business`

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `business.name` | string | ✅ | Raison sociale |
| `business.type` | string | ✅ | Type légal (ex: `Association`, `SAS`) |
| `business.registration_number` | string | ✅ | SIRET / SIREN |
| `business.registration_date` | date | ❌ | Date d'immatriculation (YYYY-MM-DD) |
| `business.contact_email` | email | ✅ | Email de contact |
| `business.phone_number` | string | ❌ | Téléphone |
| `business.trading_name` | string | ❌ | Nom commercial |
| `business.website_url` | url | ❌ | Site web |
| `business.trading_countries` | string[] | ❌ | Pays d'activité (ISO-2, ex: `["FR"]`) |
| `business.registered_address` | object | ✅ | Adresse légale (voir ci-dessous) |
| `business.trading_address` | object | ❌ | Adresse commerciale (même structure) |

#### Si `entity_type = Individual`

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `individual.first_name` | string | ✅ | Prénom |
| `individual.last_name` | string | ✅ | Nom |
| `individual.date_of_birth` | date | ✅ | Date de naissance (YYYY-MM-DD) |
| `individual.address` | object | ✅ | Adresse personnelle (voir ci-dessous) |
| `individual.email` | email | ❌ | Email |
| `individual.gender` | string | ❌ | `Male`, `Female` ou `NotSpecified` |
| `individual.nationalities` | string[] | ❌ | Nationalités (ISO-2) |
| `individual.phone_number` | string | ❌ | Téléphone |

#### Structure d'adresse

| Champ | Requis |
|-------|:------:|
| `line_one` | ✅ |
| `city` | ✅ |
| `country` | ✅ (ISO-2) |
| `postal_code` | ✅ |
| `line_two` | ❌ |
| `region` | ❌ |

### Réponse (201 Created)

```json
{
    "psp": "ryft",
    "account_id": "acct_xxxxxxxxxxxx",
    "onboarding_status": "pending"
}
```

> Retourne **409** si un compte Ryft existe déjà pour ce shop.

---

## Étape 2 — Vérifier le statut du compte

### GET /v1/shops/{shop_id}/psp/account

Retourne les données brutes du sub-account Ryft (statut KYB, documents requis, etc.).

```php
$client->shops->getPspStatus($shopId);
// Retourne les données du shop, incluant le tableau psp_accounts
```

---

## Étape 3 — Uploader des documents KYB

### POST /v1/shops/{shop_id}/psp/documents/upload

Uploade un fichier (PDF, JPG, PNG — max 10 Mo) et retourne un `file_id` à réutiliser.

```php
$result = $client->shops->uploadPspDocument(
    $shopId,
    '/chemin/vers/kbis.pdf',
    'IncorporationDocument'  // catégorie Ryft
);
// $result['file_id'] => "file_xxxxxx"
```

> Le corps doit être en `multipart/form-data`.

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `file` | file | ✅ | PDF, JPG, PNG — max 10 Mo |
| `category` | string | ✅ | Catégorie Ryft (voir tableau ci-dessous) |

### Catégories de documents Ryft

| Catégorie | Description |
|-----------|-------------|
| `ProofOfIdentity` | Pièce d'identité (passeport, CNI) |
| `ProofOfAddress` | Justificatif de domicile |
| `IncorporationDocument` | Extrait Kbis ou statuts |
| `BankStatement` | Relevé bancaire |

---

## Étape 4 — Soumettre les documents KYB

### PATCH /v1/shops/{shop_id}/psp/account

Associe les documents uploadés au sub-account Ryft.

```php
$client->shops->updatePspAccount($shopId, [
    'entity_type' => 'Business',
    'documents' => [
        [
            'type' => 'Passport',
            'category' => 'ProofOfIdentity',
            'front' => 'file_xxxxxxxxxx',
        ],
        [
            'type' => 'BankStatement',
            'category' => 'ProofOfAddress',
            'front' => 'file_yyyyyyyyyy',
        ],
    ],
]);
```

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `entity_type` | string | ✅ | `Business` ou `Individual` |
| `documents` | array | ✅ | Liste des documents (min 1) |
| `documents[].type` | string | ✅ | Type (ex: `Passport`, `DrivingLicence`) |
| `documents[].category` | string | ✅ | Catégorie Ryft |
| `documents[].front` | string | ✅ | `file_id` recto |
| `documents[].back` | string | ❌ | `file_id` verso (recto-verso) |

---

## Gestion des persons (représentants / bénéficiaires effectifs)

> Ces routes sont disponibles directement via l'API. Pas encore de méthodes dédiées dans le SDK.

### GET /v1/shops/{shop_id}/psp/persons

Liste les persons du sub-account.

### POST /v1/shops/{shop_id}/psp/persons

Crée une person (représentant légal, bénéficiaire effectif).

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `first_name` | string | ✅ | Prénom |
| `last_name` | string | ✅ | Nom |
| `business_roles` | string[] | ❌ | Ex: `["Director"]`, `["UBO"]` |
| `address` | object | ❌ | Adresse personnelle |
| `documents` | array | ❌ | Documents d'identité (même structure que PATCH account) |

### GET /v1/shops/{shop_id}/psp/persons/{personId}

Récupère une person spécifique.

### PATCH /v1/shops/{shop_id}/psp/persons/{personId}

Met à jour une person.

---

## Étape 5 — Ajouter un RIB (payout method)

### POST /v1/shops/{shop_id}/psp/payout-methods

L'IBAN est **extrait automatiquement** depuis le document RIB uploadé.

> Le corps doit être en `multipart/form-data`.

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `file` | file | ✅ | RIB (PDF, JPG, PNG — max 10 Mo) |
| `currency` | string | ✅ | Devise ISO-4217 (ex: `EUR`) |
| `country` | string | ✅ | Pays du compte bancaire (ISO-2, ex: `FR`) |
| `address` | object | ✅ | Adresse du titulaire (même structure qu'une adresse) |
| `display_name` | string | ❌ | Libellé (ex: `Compte Principal`) |

### Réponse (201 Created)

```json
{
    "payout_method_id": "pm_xxxxxxxxxxxx"
}
```

---

## Flux d'onboarding complet

```
1. POST /psp/create              → Crée le sub-account (statut: pending)
2. POST /psp/documents/upload    → Uploade les documents → file_id
3. PATCH /psp/account            → Soumet les documents KYB
4. POST /psp/persons             → Ajoute les bénéficiaires effectifs
5. POST /psp/payout-methods      → Ajoute le RIB
6. Webhook Account.updated       → Ryft met à jour le statut (→ verified)
```

> Le statut évolue automatiquement via les **webhooks Ryft** (`Account.created`, `Account.updated`).

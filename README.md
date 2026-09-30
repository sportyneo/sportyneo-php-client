# Sportyneo PHP SDK

SDK PHP natif pour l'API Sportyneo - Gestion des entités, clubs, clients, commandes et statistiques.

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

## 📋 Table des matières

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage rapide](#usage-rapide)
- [Documentation complète](#documentation-complète)
    - [Entities](#entities)
    - [Shops (Clubs)](#shops-clubs)
    - [Customers](#customers)
    - [Orders](#orders)
    - [Users](#users)
    - [Statistics](#statistics)
    - [Shop Stats](#shop-stats)
- [Gestion des erreurs](#gestion-des-erreurs)
- [Import en masse](#import-en-masse)
- [Support](#support)

---

## 🚀 Installation

### Prérequis

- PHP >= 7.4
- Extension cURL activée
- Extension JSON activée

### Installation manuelle

1. Téléchargez tous les fichiers du SDK
2. Placez-les dans votre projet PHP
3. Incluez les fichiers dans votre code :

```php
require_once 'path/to/SportyneoClient.php';
require_once 'path/to/Exceptions.php';
require_once 'path/to/BaseResource.php';
require_once 'path/to/Resources.php';
```

### Installation via Composer (recommandé)

```bash
composer require sportyneo/sdk
```

---

## ⚙️ Configuration

### Initialisation du client

```php
use Sportyneo\SDK\SportyneoClient;

$client = new SportyneoClient(
    email: 'your-email@example.com',
    password: 'your-password',
    entityId: 123,
    baseUrl: 'https://api.sportyneo.com' // Optionnel
);
```

### Options de configuration

```php
// Définir un timeout personnalisé (en secondes)
$client->setTimeout(120); // Défaut: 60 secondes

// Activer le mode debug
$client->setDebug(true);
```

---

## 🎯 Usage rapide

```php
// Lister les clubs
$shops = $client->shops->all(['page' => 1, 'per_page' => 20]);

// Créer un client
$customer = $client->customers->create([
    'mail' => 'client@example.com',
    'first_name' => 'Jean',
    'surname' => 'Dupont'
]);

// Récupérer des statistiques
$summary = $client->shopStats->summary(
    shopId: 1,
    from: '2024-01-01',
    to: '2024-12-31'
);
```

---

## 📚 Documentation complète

### Entities

```php
// Lister toutes les entités
$entities = $client->entities->all(['page' => 1]);

// Récupérer une entité spécifique
$entity = $client->entities->get(1);

// Créer une entité
$entity = $client->entities->create([
    'name' => 'Fédération Française de Football'
]);
```

**Paramètres de création :**
| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `name` | string | ✅ | Nom de l'entité (max 255) |

---

### Shops (Clubs)

```php
// Lister tous les clubs
$shops = $client->shops->all(['page' => 1]);

// Filtrer par external_id
$shops = $client->shops->all(['external_id' => 5001]);

// Trouver un club par external_id
$shop = $client->shops->findByExternalId(5001);

// Récupérer un club spécifique
$shop = $client->shops->get(1);

// Créer un club
$shop = $client->shops->create([
    'entity_id' => 1,
    'external_id' => 5002,
    'title' => 'FC Nantes',
    'phone' => '+33240000000',
    'email' => 'contact@fcnantes.fr',
    'website' => 'https://www.fcnantes.fr',
    'category' => 'Football',
    'street_name' => 'Route de Saint-Joseph',
    'postal_code' => '44300'
]);
```

**Paramètres de création :**
| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `entity_id` | integer | ✅ | ID de l'entité |
| `external_id` | integer | ✅ | ID externe (unique) |
| `title` | string | ❌ | Nom du club |
| `phone` | string | ❌ | Téléphone |
| `email` | string | ❌ | Email |
| `website` | string | ❌ | Site web |
| `category` | string | ❌ | Catégorie/Sport |
| `street_name` | string | ❌ | Nom de rue |
| `street_extended` | string | ❌ | Complément d'adresse |
| `postal_code` | string | ❌ | Code postal |

---

### Customers

```php
// Lister tous les clients
$customers = $client->customers->all(['page' => 1]);

// Filtrer par email
$customers = $client->customers->all(['mail' => 'jean@example.com']);

// Trouver un client par email
$customer = $client->customers->findByEmail('jean@example.com');

// Trouver un client par external_id
$customer = $client->customers->findByExternalId(12345);

// Créer un client
$customer = $client->customers->create([
    'external_id' => 12345,
    'first_name' => 'Marie',
    'surname' => 'Martin',
    'mail' => 'marie.martin@example.com',
    'phone' => '+33612345678',
    'birth_date' => '1995-08-20',
    'address_street' => '25 Avenue des Champs',
    'address_code' => '69000',
    'address_locality' => 'Lyon',
    'address_country' => 'France'
]);
```

**Paramètres de création :**
| Champ | Type | Requis | Description |
|-------|------|--------|-------------|
| `external_id` | integer | 🔶 | ID externe (requis si mail absent) |
| `mail` | string | 🔶 | Email (requis si external_id absent) |
| `first_name` | string | ❌ | Prénom |
| `surname` | string | ❌ | Nom |
| `phone` | string | ❌ | Téléphone |
| `birth_date` | date | ❌ | Date de naissance (YYYY-MM-DD) |
| `address_street` | string | ❌ | Adresse |
| `address_code` | string | ❌ | Code postal |
| `address_locality` | string | ❌ | Ville |
| `address_country` | string | ❌ | Pays |

---

### Orders

```php
// Lister toutes les commandes
$orders = $client->orders->all(['page' => 1]);

// Récupérer une commande spécifique
$order = $client->orders->get(1);

// Créer une commande
$order = $client->orders->create([
    'shop_id' => 1,
    'customer_id' => 1,
    'internal_id' => 1001,
    'external_id' => 'EXT-2024-001',
    'reference' => 'REF-20240120-001',
    'status' => 'paid',
    'payment_method' => 'card',
    'instalment' => '1x',
    'paid_date' => '2024-01-20',
    'subtotal' => 14000,      // en centimes
    'refund' => 0,
    'coupons' => 500,
    'discounts' => 0,
    'donations' => 0,
    'insurances' => 500,
    'deliveries' => 1000,
    'fees' => 200,
    'fees_services' => 300,
    'amount' => 15000,        // en centimes
    'refund_club' => 0,
    'amount_club' => 14500
]);
```

**⚠️ Important : Tous les montants sont en centimes** (15000 = 150,00€)

---

### Users

```php
// Lister tous les utilisateurs
$users = $client->users->all();

// Récupérer un utilisateur spécifique
$user = $client->users->get(1);

// Créer un utilisateur
$user = $client->users->create([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'SecurePass123!',
    'entity_id' => 1
]);
```

---

### Statistics

```php
// Lister des clubs par filtres
$clubs = $client->statistics->shops(
    sport: 'Football',
    region: 'Auvergne-Rhône-Alpes',
    department: '69'
);

// Clubs inactifs (sans commande depuis X jours)
$inactive = $client->statistics->inactiveShops(days: 30);

// Volumes de paiement par type
$volumes = $client->statistics->paymentVolumes(
    sport: 'Football',
    startDate: '2024-01-01',
    endDate: '2024-12-31'
);
```

---

### Shop Stats

```php
// Résumé des statistiques d'un club
$summary = $client->shopStats->summary(
    shopId: 1,
    from: '2024-01-01',
    to: '2024-12-31'
);

// Statistiques en temps réel
$liveSummary = $client->shopStats->summaryLive(
    shopId: 1,
    from: '2024-01-01',
    to: '2024-12-31'
);

// Statistiques de tous les clubs
$allShops = $client->shopStats->summaryLiveAll(
    from: '2024-01-01',
    to: '2024-12-31'
);

// Version paginée
$paginated = $client->shopStats->summaryLivePaginated(
    from: '2024-01-01',
    to: '2024-12-31',
    perPage: 50
);
```

---

## 🚨 Gestion des erreurs

Le SDK propose plusieurs exceptions pour gérer les erreurs :

```php
use Sportyneo\SDK\Exceptions\ApiException;
use Sportyneo\SDK\Exceptions\AuthenticationException;
use Sportyneo\SDK\Exceptions\ValidationException;
use Sportyneo\SDK\Exceptions\NotFoundException;

try {
    $shop = $client->shops->get(999);
} catch (NotFoundException $e) {
    echo "Ressource non trouvée : " . $e->getMessage();
} catch (ValidationException $e) {
    echo "Erreurs de validation :\n";
    foreach ($e->getErrors() as $field => $errors) {
        echo "  - $field : " . implode(', ', $errors) . "\n";
    }
} catch (AuthenticationException $e) {
    echo "Erreur d'authentification : " . $e->getMessage();
} catch (ApiException $e) {
    echo "Erreur API : " . $e->getMessage();
    echo "Code HTTP : " . $e->getCode();
}
```

### Types d'exceptions

| Exception | Code HTTP | Description |
|-----------|-----------|-------------|
| `AuthenticationException` | 401 | Erreur d'authentification |
| `NotFoundException` | 404 | Ressource non trouvée |
| `ValidationException` | 422 | Erreurs de validation |
| `ApiException` | Autre | Erreur générique |

---

## 📦 Import en masse

### Exemple : Import de clubs

```php
function importShops(SportyneoClient $client, array $shops): array
{
    $results = ['created' => 0, 'skipped' => 0, 'failed' => 0];
    
    foreach ($shops as $shopData) {
        try {
            // Vérifier si le club existe déjà
            $existing = $client->shops->findByExternalId($shopData['external_id']);
            
            if ($existing) {
                $results['skipped']++;
                echo "Club {$shopData['external_id']} existe déjà\n";
                continue;
            }
            
            // Créer le club
            $client->shops->create($shopData);
            $results['created']++;
            echo "Club {$shopData['external_id']} créé\n";
            
        } catch (ValidationException $e) {
            $results['failed']++;
            echo "Erreur validation : " . $e->getMessage() . "\n";
        } catch (ApiException $e) {
            $results['failed']++;
            echo "Erreur API : " . $e->getMessage() . "\n";
        }
    }
    
    return $results;
}

// Utilisation
$shopsToImport = [
    [
        'entity_id' => 1,
        'external_id' => 5003,
        'title' => 'Club Example',
        'category' => 'Football',
    ],
    // ... autres clubs
];

$results = importShops($client, $shopsToImport);
echo "Import terminé : " . json_encode($results) . "\n";
```

### Exemple : Import de clients

```php
function importCustomers(SportyneoClient $client, array $customers): array
{
    $results = ['created' => 0, 'skipped' => 0, 'failed' => 0];
    
    foreach ($customers as $customerData) {
        try {
            // Vérifier si le client existe
            $email = $customerData['mail'] ?? null;
            if ($email) {
                $existing = $client->customers->findByEmail($email);
                if ($existing) {
                    $results['skipped']++;
                    continue;
                }
            }
            
            // Créer le client
            $client->customers->create($customerData);
            $results['created']++;
            
        } catch (ApiException $e) {
            $results['failed']++;
        }
    }
    
    return $results;
}
```

---

## 🔍 Bonnes pratiques

### 1. Vérifier l'existence avant création

```php
// Pour les clubs
$shop = $client->shops->findByExternalId($externalId);
if (!$shop) {
    $shop = $client->shops->create($shopData);
}

// Pour les clients
$customer = $client->customers->findByEmail($email);
if (!$customer) {
    $customer = $client->customers->create($customerData);
}
```

### 2. Utiliser les filtres pour optimiser

```php
// Au lieu de récupérer tous les clients et filtrer en PHP
$allCustomers = $client->customers->all();
$filtered = array_filter($allCustomers['data'], fn($c) => $c['mail'] === $email);

// Utilisez les filtres API
$result = $client->customers->all(['mail' => $email]);
$customer = $result['data'][0] ?? null;
```

### 3. Gérer la pagination

```php
$page = 1;
$allShops = [];

do {
    $result = $client->shops->all(['page' => $page, 'per_page' => 100]);
    $allShops = array_merge($allShops, $result['data']);
    $page++;
} while ($page <= $result['last_page']);
```

---

## 📖 Exemples complets

Consultez le fichier `examples.php` pour des exemples détaillés d'utilisation de toutes les fonctionnalités.

---

## 🆘 Support

- **Documentation API :** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- **Issues :** Créez une issue sur le repository
- **Email :** support@sportyneo.com

---

## 📄 Licence

MIT License - Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 🔄 Changelog

### Version 1.0.0 (2024-01-20)
- ✨ Version initiale du SDK
- 🎯 Support complet de l'API v1
- 📦 Import en masse
- 🚨 Gestion des erreurs
- 📚 Documentation complète

---

**Développé avec ❤️ par Sportyneo**
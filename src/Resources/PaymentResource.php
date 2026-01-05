<?php
namespace Sportyneo\SDK\Resources;

use Sportyneo\SDK\Contracts\OrderItemCategory;
use Sportyneo\SDK\Contracts\OrderItemType;
use Sportyneo\SDK\Exceptions\ValidationException;

class PaymentResource extends BaseResource
{
    protected $endpoint = '/payments';

    /**
     * Créer une session de paiement et obtenir l'URL du tunnel
     *
     * @param array $data Les données du panier
     * @return array Les informations de la session de paiement
     */
    public function create(array $data): array
    {
        $requiredFields = [
            'shop_id' => 'integer',
            'customer_id' => 'integer',
            'internal_id' => 'string|integer',
            'amount' => 'numeric',
            'items' => 'array',
            'return_url' => 'url',
            'error_url' => 'url',
            'back_url' => 'url',
        ];

        foreach ($requiredFields as $field => $type) {
            if (!isset($data[$field])) {
                throw new ValidationException("Le champ '{$field}' est requis");
            }
            $this->validateType($field, $data[$field], $type);
        }

        if (empty($data['items'])) {
            throw new ValidationException("Le champ 'items' ne peut pas être vide");
        }

        if ($data['amount'] <= 0) {
            throw new ValidationException("Le champ 'amount' doit être supérieur à 0");
        }

        $itemRequiredFields = [
            'id' => 'integer',
            'name' => 'string',
            'quantity' => 'integer',
            'unit_price' => 'numeric',
            'type' => OrderItemType::class,
            'category' => OrderItemCategory::class,
            'vat_rate' => 'numeric',
        ];

        $itemOptionalFields = [
            'options' => 'array',
            'metadata' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];

        foreach ($data['items'] as $index => $item) {
            foreach ($itemRequiredFields as $field => $type) {
                if (!isset($item[$field])) {
                    throw new ValidationException("Le champ 'items[{$index}].{$field}' est requis");
                }
                $this->validateType("items[{$index}].{$field}", $item[$field], $type);
            }

            foreach ($itemOptionalFields as $field => $type) {
                if (isset($item[$field])) {
                    $this->validateType("items[{$index}].{$field}", $item[$field], $type);
                }
            }

            if ($item['quantity'] < 1) {
                throw new ValidationException("Le champ 'items[{$index}].quantity' doit être au minimum 1");
            }

            if ($item['unit_price'] < 0) {
                throw new ValidationException("Le champ 'items[{$index}].unit_price' ne peut pas être négatif");
            }

            if ($item['vat_rate'] < 0 || $item['vat_rate'] > 100) {
                throw new ValidationException("Le champ 'items[{$index}].vat_rate' doit être entre 0 et 100");
            }

            if ($item['quantity'] > 1
                && $item['type'] === OrderItemType::PRODUCT
                && in_array($item['category'], [OrderItemCategory::LICENSES, OrderItemCategory::STAGES])
            ) {
                throw new ValidationException(
                    "La quantité d'une licence ou d'un stage ne peut pas être supérieure à 1 (1 produit = 1 personne)"
                );
            }

            if (isset($item['start_date'], $item['end_date'])) {
                $startDate = $item['start_date'] instanceof \DateTimeInterface
                    ? $item['start_date']
                    : new \DateTime($item['start_date']);
                $endDate = $item['end_date'] instanceof \DateTimeInterface
                    ? $item['end_date']
                    : new \DateTime($item['end_date']);

                if ($endDate < $startDate) {
                    throw new ValidationException(
                        "Le champ 'items[{$index}].end_date' doit être postérieur à 'start_date'"
                    );
                }
            }

            if (isset($item['options'])) {
                $this->validateItemOptions($item['options'], $index);
            }

            if (isset($item['metadata'])) {
                $this->validateItemMetadata($item['metadata'], $index);
            }
        }

        return $this->client->post($this->endpoint, $data);
    }

    private function validateType(string $field, mixed $value, string $type): void
    {
        $valid = match ($type) {
            'integer' => is_int($value),
            'string' => is_string($value),
            'string|integer' => is_string($value) || is_int($value),
            'numeric' => is_numeric($value),
            'array' => is_array($value),
            'boolean' => is_bool($value),
            'url' => is_string($value) && filter_var($value, FILTER_VALIDATE_URL),
            'email' => is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL),
            'date' => $value instanceof \DateTimeInterface || strtotime($value) !== false,
            OrderItemType::class => $value instanceof OrderItemType,
            OrderItemCategory::class => $value instanceof OrderItemCategory,
            default => true,
        };

        if (!$valid) {
            throw new ValidationException("Le champ '{$field}' doit être de type {$type}");
        }
    }

    private function validateItemOptions(array $options, int $index): void
    {
        $optionalFields = [
            'external_id' => 'string',
            'is_insurable' => 'boolean',
        ];

        foreach ($optionalFields as $field => $type) {
            if (isset($options[$field])) {
                $this->validateType("items[{$index}].options.{$field}", $options[$field], $type);
            }
        }
    }

    private function validateItemMetadata(array $metadata, int $index): void
    {
        $optionalFields = [
            'first_name' => 'string',
            'last_name' => 'string',
            'birth_date' => 'date',
            'email' => 'email',
            'phone' => 'string',
        ];

        foreach ($optionalFields as $field => $type) {
            if (isset($metadata[$field])) {
                $this->validateType("items[{$index}].metadata.{$field}", $metadata[$field], $type);
            }
        }
    }
}

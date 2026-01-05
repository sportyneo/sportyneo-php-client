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
        $requiredFields = ['shop_id', 'customer_id', 'internal_id', 'amount', 'items'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new ValidationException("Le champ '{$field}' est requis");
            }
        }

        if (!is_array($data['items'])) {
            throw new ValidationException("Le champ 'items' doit être un tableau");
        }

        foreach($data['items'] as $item) {
            if ($item['quantity'] > 1 && $item['type'] === OrderItemType::PRODUCT &&
                ($item['category'] === OrderItemCategory::LICENSES || $item['category'] === OrderItemCategory::STAGES)) {
                throw new ValidationException("La quantité d'une licence ou d'un stage ne peut pas être supérieur à 1. (1 produit = 1 personne)");
            }
        }

        return $this->client->post($this->endpoint, $data);
    }
}

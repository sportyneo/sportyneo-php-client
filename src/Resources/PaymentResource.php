<?php
namespace Sportyneo\SDK\Resources;

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

        return $this->client->post($this->endpoint, $data);
    }
}

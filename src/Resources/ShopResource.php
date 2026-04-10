<?php

namespace Sportyneo\SDK\Resources;

class ShopResource extends BaseResource
{
    protected $endpoint = '/shops';

    /**
     * Find shop by external ID
     *
     * @param int $externalId
     * @return array|null
     */
    public function findByExternalId(int $externalId): ?array
    {
        $result = $this->client->get($this->endpoint, ['external_id' => $externalId]);
        return $result['data'][0] ?? null;
    }

    /**
     * Get PSP accounts status for a shop.
     * Returns the shop data including the psp_accounts array.
     *
     * @param int $shopId
     * @return array
     */
    public function getPspStatus(int $shopId): array
    {
        return $this->client->get($this->endpoint.'/'.$shopId);
    }

    /**
     * Crée le compte PSP Ryft pour un shop via onboarding NonHosted (full API).
     * Doit être appelé après la création du shop.
     *
     * @param int    $shopId  Identifiant du shop
     * @param array  $data    Données d'onboarding :
     *   - entity_type          string  "Business" ou "Individual" (requis)
     *   - terms_of_service     bool    true = acceptation CGU (requis)
     *   - business             array   Données entreprise (requis si entity_type=Business)
     *     - name               string
     *     - type               string  Ex: "Corporation"
     *     - registration_number string
     *     - registration_date  string  Format Y-m-d
     *     - contact_email      string
     *     - phone_number       string
     *     - trading_name       string
     *     - website_url        string
     *     - trading_countries  string[]
     *     - registered_address array   { line_one, line_two?, city, country (ISO 3166-1), postal_code, region? }
     *     - trading_address    array   (optionnel, même structure que registered_address)
     *   - individual           array   Données personne physique (requis si entity_type=Individual)
     *     - first_name         string
     *     - last_name          string
     *     - middle_names       string
     *     - email              string
     *     - date_of_birth      string  Format Y-m-d
     *     - country_of_birth   string  ISO 3166-1
     *     - nationalities      string[]
     *     - phone_number       string
     *     - gender             string  "Male", "Female" ou "NotSpecified"
     *     - address            array   { line_one, line_two?, city, country (ISO 3166-1), postal_code, region? }
     * @return array { psp, account_id, onboarding_status }
     */
    public function createPsp(int $shopId, array $data): array
    {
        return $this->client->post($this->endpoint.'/'.$shopId.'/psp/create', $data);
    }
}

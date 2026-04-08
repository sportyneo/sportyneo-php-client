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
        return $this->client->get($this->endpoint . '/' . $shopId);
    }

    /**
     * Generate a PSP onboarding link for a shop.
     *
     * @param int    $shopId
     * @param string $psp         e.g. "ryft"
     * @param string $redirectUrl URL to redirect after onboarding
     * @return array              { url, psp, account_id, onboarding_status }
     */
    public function getPspOnboardingLink(int $shopId, string $psp, string $redirectUrl): array
    {
        return $this->client->post($this->endpoint . '/' . $shopId . '/psp-onboarding-link', [
            'psp' => $psp,
            'redirect_url' => $redirectUrl,
        ]);
    }
}
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
}
<?php
namespace Sportyneo\SDK\Resources;

class OrderResource extends BaseResource
{
    protected $endpoint = '/orders';

    public function findByInternalId(int $internalId): ?array
    {
        $result = $this->client->get($this->endpoint, ['internal_id' => $internalId]);
        return $result['data'][0] ?? null;
    }
}

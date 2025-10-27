<?php
namespace Sportyneo\SDK\Resources;

/**
 * Customer Resource
 */
class CustomerResource extends BaseResource
{
    protected $endpoint = '/customers';

    /**
     * Find customer by email
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $result = $this->client->get($this->endpoint, ['mail' => $email]);
        return $result['data'][0] ?? null;
    }

    /**
     * Find customer by external ID
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
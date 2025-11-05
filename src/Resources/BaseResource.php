<?php

namespace Sportyneo\SDK\Resources;

use Sportyneo\SDK\Client\Client;

/**
 * Base Resource class
 */
abstract class BaseResource
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $endpoint;

    /**
     * BaseResource constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * List all resources with pagination
     *
     * @param array $params
     * @return array
     */
    public function all(array $params = []): array
    {
        return $this->client->get($this->endpoint, $params);
    }

    /**
     * Get a specific resource by ID
     *
     * @param int $id
     * @return array
     */
    public function get(int $id): array
    {
        return $this->client->get($this->endpoint . '/' . $id);
    }

    /**
     * Create a new resource
     *
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        return $this->client->post($this->endpoint, $data);
    }

    /**
     * Update an existing resource
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data): array
    {
        return $this->client->put($this->endpoint . '/' . $id, $data);
    }

    /**
     * Delete a resource
     *
     * @param int $id
     * @return array
     */
    public function delete(int $id): array
    {
        return $this->client->delete($this->endpoint . '/' . $id);
    }
}
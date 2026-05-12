<?php

namespace Sportyneo\SDK\Client;

use Sportyneo\SDK\Exceptions\ApiException;
use Sportyneo\SDK\Exceptions\AuthenticationException;
use Sportyneo\SDK\Exceptions\ValidationException;
use Sportyneo\SDK\Exceptions\NotFoundException;
use Sportyneo\SDK\Resources\EntityResource;
use Sportyneo\SDK\Resources\InvitationResource;
use Sportyneo\SDK\Resources\PaymentResource;
use Sportyneo\SDK\Resources\ShopResource;
use Sportyneo\SDK\Resources\CustomerResource;
use Sportyneo\SDK\Resources\OrderResource;
use Sportyneo\SDK\Resources\UserResource;
use Sportyneo\SDK\Resources\StatisticResource;
use Sportyneo\SDK\Resources\ShopStatResource;

/**
 * Sportyneo API SDK Client
 *
 * @version 1.0.0
 * @author Sportyneo
 * @link https://api.sportyneo.com/
 */
class Client
{
    /** @var string */
    private $baseUrl;

    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var int */
    private $entityId;

    /** @var array */
    private $headers = [];

    /** @var int */
    private $timeout = 60;

    /** @var bool */
    private $debug = false;

    /** @var EntityResource */
    public $entities;

    /** @var ShopResource */
    public $shops;

    /** @var CustomerResource */
    public $customers;

    /** @var OrderResource */
    public $orders;

    /** @var UserResource */
    public $users;

    /** @var StatisticResource */
    public $statistics;

    /** @var ShopStatResource */
    public $shopStats;

    /** @var PaymentResource */
    public $payments;

    /** @var InvitationResource */
    public $invitations;

    /**
     * SportyneoClient constructor.
     *
     * @param string $email User email for authentication
     * @param string $password User password for authentication
     * @param int $entityId Entity ID
     * @param string $baseUrl Base URL of the API (default: https://api.sportyneo.com)
     */
    public function __construct(string $email, string $password, int $entityId, string $baseUrl = 'https://api.sportyneo.com')
    {
        $this->email = $email;
        $this->password = $password;
        $this->entityId = $entityId;
        $this->baseUrl = rtrim($baseUrl, '/');

        $this->setupHeaders();
        $this->initializeResources();
    }

    /**
     * Setup default headers for API requests
     */
    private function setupHeaders(): void
    {
        $this->headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->email . ':' . $this->password),
            'Sportyneo-Entity-Id: ' . $this->entityId,
        ];
    }

    /**
     * Initialize resource endpoints
     */
    private function initializeResources(): void
    {
        $this->entities = new EntityResource($this);
        $this->shops = new ShopResource($this);
        $this->customers = new CustomerResource($this);
        $this->orders = new OrderResource($this);
        $this->users = new UserResource($this);
        $this->statistics = new StatisticResource($this);
        $this->shopStats = new ShopStatResource($this);
        $this->payments     = new PaymentResource($this);
        $this->invitations  = new InvitationResource($this);
    }

    /**
     * Set request timeout in seconds
     *
     * @param int $timeout
     * @return $this
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Enable debug mode
     *
     * @param bool $debug
     * @return $this
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Execute HTTP GET request
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws ApiException
     */
    public function get(string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $this->request('GET', $url);
    }

    /**
     * Execute HTTP POST request
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function post(string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        return $this->request('POST', $url, $data);
    }

    /**
     * Execute HTTP PUT request
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function put(string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        return $this->request('PUT', $url, $data);
    }

    /**
     * Execute HTTP PATCH request
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function patch(string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        return $this->request('PATCH', $url, $data);
    }

    /**
     * Execute HTTP DELETE request
     *
     * @param string $endpoint
     * @return array
     * @throws ApiException
     */
    public function delete(string $endpoint): array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        return $this->request('DELETE', $url);
    }

    /**
     * Upload a file via multipart/form-data POST
     *
     * @param string $endpoint
     * @param string $filePath   Absolute path to the local file
     * @param array  $fields     Additional form fields
     * @return array
     * @throws ApiException
     */
    public function postFile(string $endpoint, string $filePath, array $fields = []): array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;

        $ch = curl_init();

        $postFields = $fields;
        $postFields['file'] = new \CURLFile($filePath);

        $headers = array_values(array_filter(
            $this->headers,
            fn ($h) => !str_starts_with($h, 'Content-Type')
        ));

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        if ($this->debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        $response  = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error     = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new ApiException('cURL Error: ' . $error);
        }

        return $this->handleResponse($response, $statusCode);
    }

    /**
     * Execute HTTP request using cURL
     *
     * @param string $method
     * @param string $url
     * @param array|null $data
     * @return array
     * @throws ApiException
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws NotFoundException
     */
    private function request(string $method, string $url, ?array $data = null): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->normalizeEnums($data)));
        }

        if ($this->debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new ApiException('cURL Error: ' . $error);
        }

        return $this->handleResponse($response, $statusCode);
    }

    /**
     * Handle API response and throw appropriate exceptions
     *
     * @param string $response
     * @param int $statusCode
     * @return array
     * @throws ApiException
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws NotFoundException
     */
    private function handleResponse(string $response, int $statusCode): array
    {
        $data = json_decode($response, true);

        if ($statusCode >= 200 && $statusCode < 300) {
            return $data ?? [];
        }

        $message = $data['message'] ?? $data['error'] ?? 'Unknown error';

        switch ($statusCode) {
            case 401:
                throw new AuthenticationException($message, $statusCode);
            case 404:
                throw new NotFoundException($message, $statusCode);
            case 422:
                throw new ValidationException($message, $statusCode, $data['errors'] ?? []);
            default:
                throw new ApiException($message, $statusCode);
        }
    }

    /**
     * Get base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    private function normalizeEnums(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value instanceof \UnitEnum) {
                $data[$key] = $value instanceof \BackedEnum ? $value->value : $value->name;
            } elseif (is_array($value)) {
                $data[$key] = $this->normalizeEnums($value);
            }
        }

        return $data;
    }

    /**
     * Get entity ID
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }
}
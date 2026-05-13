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

    /** @var string */
    private $token;

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
     * @param string $email    User email for authentication
     * @param string $password User password for authentication
     * @param int    $entityId Entity ID
     * @param string $baseUrl  Base URL of the API
     * @throws AuthenticationException
     * @throws ApiException
     */
    public function __construct(string $email, string $password, int $entityId, string $baseUrl = 'https://api.sportyneo.com')
    {
        $this->email    = $email;
        $this->password = $password;
        $this->entityId = $entityId;
        $this->baseUrl  = rtrim($baseUrl, '/');

        $this->authenticate();
        $this->initializeResources();
    }

    /**
     * Fetch a Bearer token from POST /api/v1/auth/token and rebuild headers.
     *
     * @throws AuthenticationException
     * @throws ApiException
     */
    private function authenticate(): void
    {
        $url = $this->baseUrl . '/api/v1/auth/token';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode([
                'email'    => $this->email,
                'password' => $this->password,
            ]),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
            ],
        ]);

        if ($this->debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new ApiException('cURL Error: ' . $error);
        }

        if ($status !== 200) {
            $data = json_decode($body, true);
            $msg  = $data['message'] ?? $data['error'] ?? 'Authentication failed';
            throw new AuthenticationException($msg, $status);
        }

        $data = json_decode($body, true);

        if (empty($data['token'])) {
            throw new AuthenticationException('No token returned by authentication endpoint', 200);
        }

        $this->token = $data['token'];
        $this->buildHeaders();
    }

    /**
     * Rebuild the headers array with the current Bearer token.
     */
    private function buildHeaders(): void
    {
        $this->headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
            'Sportyneo-Entity-Id: ' . $this->entityId,
        ];
    }

    /**
     * Initialize resource endpoints
     */
    private function initializeResources(): void
    {
        $this->entities   = new EntityResource($this);
        $this->shops      = new ShopResource($this);
        $this->customers  = new CustomerResource($this);
        $this->orders     = new OrderResource($this);
        $this->users      = new UserResource($this);
        $this->statistics = new StatisticResource($this);
        $this->shopStats  = new ShopStatResource($this);
        $this->payments   = new PaymentResource($this);
        $this->invitations = new InvitationResource($this);
    }

    /**
     * Set request timeout in seconds
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Enable debug mode
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Execute HTTP GET request
     *
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
     * @throws ApiException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $this->baseUrl . '/api/v1' . $endpoint, $data);
    }

    /**
     * Execute HTTP PUT request
     *
     * @throws ApiException
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $this->baseUrl . '/api/v1' . $endpoint, $data);
    }

    /**
     * Execute HTTP PATCH request
     *
     * @throws ApiException
     */
    public function patch(string $endpoint, array $data = []): array
    {
        return $this->request('PATCH', $this->baseUrl . '/api/v1' . $endpoint, $data);
    }

    /**
     * Execute HTTP DELETE request
     *
     * @throws ApiException
     */
    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $this->baseUrl . '/api/v1' . $endpoint);
    }

    /**
     * Upload a file via multipart/form-data POST
     *
     * @throws ApiException
     */
    public function postFile(string $endpoint, string $filePath, array $fields = []): array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;

        $result = $this->executeFileRequest($url, $filePath, $fields);

        if ($result['status'] === 401) {
            $this->authenticate();
            $result = $this->executeFileRequest($url, $filePath, $fields);
        }

        return $this->handleResponse($result['body'], $result['status']);
    }

    /**
     * Execute HTTP request using cURL, with one automatic re-auth retry on 401.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws NotFoundException
     */
    private function request(string $method, string $url, ?array $data = null): array
    {
        $result = $this->executeRequest($method, $url, $data);

        if ($result['status'] === 401) {
            $this->authenticate();
            $result = $this->executeRequest($method, $url, $data);
        }

        return $this->handleResponse($result['body'], $result['status']);
    }

    /**
     * Run a single cURL request and return raw status + body.
     *
     * @return array{status: int, body: string}
     * @throws ApiException
     */
    private function executeRequest(string $method, string $url, ?array $data): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if ($this->debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new ApiException('cURL Error: ' . $error);
        }

        return ['status' => $status, 'body' => $body];
    }

    /**
     * @return array{status: int, body: string}
     * @throws ApiException
     */
    private function executeFileRequest(string $url, string $filePath, array $fields): array
    {
        $ch = curl_init();

        $postFields         = $fields;
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

        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new ApiException('cURL Error: ' . $error);
        }

        return ['status' => $status, 'body' => $body];
    }

    /**
     * Handle API response and throw appropriate exceptions
     *
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
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get entity ID
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }
}

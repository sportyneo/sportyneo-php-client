<?php
namespace Sportyneo\SDK\Resources;

/**
 * Shop Stat Resource
 */
class ShopStatResource extends BaseResource
{
    protected $endpoint = '/shop-stats';

    /**
     * Get aggregated summary for a shop
     *
     * @param int $shopId
     * @param string|null $from
     * @param string|null $to
     * @return array
     */
    public function summary(int $shopId, ?string $from = null, ?string $to = null): array
    {
        $params = array_filter([
            'shop_id' => $shopId,
            'from' => $from,
            'to' => $to,
        ]);

        return $this->client->get($this->endpoint . '/summary', $params);
    }

    /**
     * Get live summary for a shop (calculated from orders)
     *
     * @param int $shopId
     * @param string|null $from
     * @param string|null $to
     * @return array
     */
    public function summaryLive(int $shopId, ?string $from = null, ?string $to = null): array
    {
        $params = array_filter([
            'shop_id' => $shopId,
            'from' => $from,
            'to' => $to,
        ]);

        return $this->client->get($this->endpoint . '/summary-live', $params);
    }

    /**
     * Get live summary for all shops
     *
     * @param string|null $from
     * @param string|null $to
     * @return array
     */
    public function summaryLiveAll(?string $from = null, ?string $to = null): array
    {
        $params = array_filter([
            'from' => $from,
            'to' => $to,
        ]);

        return $this->client->get($this->endpoint . '/summary-live-all', $params);
    }

    /**
     * Get live summary for all shops (paginated)
     *
     * @param string|null $from
     * @param string|null $to
     * @param int $perPage
     * @return array
     */
    public function summaryLivePaginated(?string $from = null, ?string $to = null, int $perPage = 15): array
    {
        $params = array_filter([
            'from' => $from,
            'to' => $to,
            'per_page' => $perPage,
        ]);

        return $this->client->get($this->endpoint . '/summary-live-paginated', $params);
    }
}

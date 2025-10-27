<?php
namespace Sportyneo\SDK\Resources;


/**
 * Statistic Resource
 */
class StatisticResource extends BaseResource
{
    protected $endpoint = '/statistics';

    /**
     * Get shops list filtered by sport, region, department
     *
     * @param string|null $sport
     * @param string|null $region
     * @param string|null $department
     * @return array
     */
    public function shops(?string $sport = null, ?string $region = null, ?string $department = null): array
    {
        $params = array_filter([
            'sport' => $sport,
            'region' => $region,
            'department' => $department,
        ]);

        return $this->client->get($this->endpoint . '/shops', $params);
    }

    /**
     * Get inactive shops (without orders for X days)
     *
     * @param int $days
     * @return array
     */
    public function inactiveShops(int $days = 30): array
    {
        return $this->client->get($this->endpoint . '/inactive-shops', ['days' => $days]);
    }

    /**
     * Get payment volumes by type (1x, 4x, 10x, 12x)
     *
     * @param string|null $sport
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function paymentVolumes(?string $sport = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $params = array_filter([
            'sport' => $sport,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return $this->client->get($this->endpoint . '/payment-volumes', $params);
    }
}

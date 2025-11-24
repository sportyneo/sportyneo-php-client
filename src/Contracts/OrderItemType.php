<?php

namespace Sportyneo\SDK\Contracts;

enum OrderItemType: int
{
    case PRODUCT = 1;
    case INSURANCE = 2;
    case FEES = 3;
    case FINANCIAL_FEES = 4;
    case DISCOUNT = 5;
    case COUPON = 6;
    case DONATION = 7;
    case ETICKET = 8;
    case DELIVERY = 9;

    public function slug(): string
    {
        return match($this) {
            self::PRODUCT => 'product',
            self::INSURANCE => 'insurance',
            self::FEES => 'fees',
            self::FINANCIAL_FEES => 'financial_fees',
            self::DISCOUNT => 'discount',
            self::COUPON => 'coupon',
            self::DONATION => 'donation',
            self::ETICKET => 'eticket',
            self::DELIVERY => 'delivery',
        };
    }

    public static function fromSlug(string $slug): self
    {
        return match($slug) {
            'product' => self::PRODUCT,
            'insurance' => self::INSURANCE,
            'fees' => self::FEES,
            'financial_fees' => self::FINANCIAL_FEES,
            'discount' => self::DISCOUNT,
            'coupon' => self::COUPON,
            'donation' => self::DONATION,
            'eticket' => self::ETICKET,
            'delivery' => self::DELIVERY,
            default => throw new \ValueError("Invalid slug: {$slug}"),
        };
    }

    public function label(): string
    {
        return match($this) {
            self::PRODUCT => 'Produit',
            self::INSURANCE => 'Assurance',
            self::FEES => 'Frais',
            self::FINANCIAL_FEES => 'Frais financiers',
            self::DISCOUNT => 'Réduction',
            self::COUPON => 'Coupon',
            self::DONATION => 'Don',
            self::ETICKET => 'E-Ticket',
            self::DELIVERY => 'Livraison',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PRODUCT => '📦',
            self::INSURANCE => '🛡️',
            self::FEES => '💳',
            self::FINANCIAL_FEES => '🏦',
            self::DISCOUNT => '🎁',
            self::COUPON => '🎟️',
            self::DONATION => '❤️',
            self::ETICKET => '🎫',
            self::DELIVERY => '🚚',
        };
    }

    public function isPositiveAmount(): bool
    {
        return !in_array($this, [self::DISCOUNT, self::COUPON]);
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
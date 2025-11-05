<?php

namespace Sportyneo\SDK\Contracts;

enum OrderDeliveryStatus: int
{
    case Pending = 1;
    case Shipped = 2;
    case Delivered = 3;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        return match ($label) {
            'Pending' => self::Pending,
            'Shipped' => self::Shipped,
            'Delivered' => self::Delivered,
            default => null,
        };
    }

    public static function getIdFromLabel(string $label): ?int
    {
        $enum = self::fromLabel($label);

        return $enum?->value;
    }

    public static function getDefault(): self
    {
        return self::Pending;
    }

    public static function getSelectArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }

    public static function validationRules(): array
    {
        return [
            'required',
            function ($attribute, $value, $fail) {
                if (is_numeric($value) && ! self::tryFrom((int) $value)) {
                    $fail("La valeur de $attribute n'est pas un identifiant de statut de livraison valide.");
                }

                if (is_string($value) && ! is_numeric($value) && ! self::fromLabel($value)) {
                    $fail("La valeur de $attribute n'est pas un statut de livraison valide.");
                }
            },
            'max:255',
        ];
    }

    public static function fromInput($value): ?self
    {
        if (is_numeric($value)) {
            return self::tryFrom((int) $value);
        }

        if (is_string($value)) {
            return self::fromLabel($value);
        }

        return null;
    }
}

<?php

namespace Sportyneo\SDK\Enums;

enum OrderStatus: int
{
    case Cart = 1;
    case Pending = 2;
    case Paid = 3;
    case Cancelled = 4;
    case Failed = 5;
    case Reimbursed = 6;
    case Issued = 7;
    case Saved = 8;

    public function label(): string
    {
        return match ($this) {
            self::Cart => 'Cart',
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::Cancelled => 'Cancelled',
            self::Failed => 'Failed',
            self::Reimbursed => 'Reimbursed',
            self::Issued => 'Issued',
            self::Saved => 'Saved',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        return match ($label) {
            'Cart' => self::Cart,
            'Pending' => self::Pending,
            'Paid' => self::Paid,
            'Cancelled' => self::Cancelled,
            'Failed' => self::Failed,
            'Reimbursed' => self::Reimbursed,
            'Issued' => self::Issued,
            'Saved' => self::Saved,
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
        return self::Cart;
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
                    $fail("La valeur de $attribute n'est pas un identifiant de statut de commande valide.");
                }

                if (is_string($value) && ! is_numeric($value) && ! self::fromLabel($value)) {
                    $fail("La valeur de $attribute n'est pas un statut de commande valide.");
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

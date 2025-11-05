<?php

namespace Sportyneo\SDK\Contracts;

use Illuminate\Validation\Rules\Enum as EnumRule;

enum Instalment: int
{
    case OneTimeCard = 1;   // 1XC
    case OneTimeDebit = 2;  // 1XD
    case ThreeTimes = 3;    // 3X
    case FourTimes = 4;     // 4X
    case TenTimes = 10;     // 10X
    case TwelveTimes = 12;  // 12X

    public function label(): string
    {
        return match ($this) {
            self::OneTimeCard => 'CB1XC',
            self::OneTimeDebit => 'CB1XD',
            self::ThreeTimes => 'CB3X',
            self::FourTimes => 'CB4X',
            self::TenTimes => 'CB10X',
            self::TwelveTimes => 'CB12X',
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            self::OneTimeCard => 'Paiement unique par carte',
            self::OneTimeDebit => 'Paiement unique par prélèvement',
            self::ThreeTimes => 'Paiement en 3 fois',
            self::FourTimes => 'Paiement en 4 fois',
            self::TenTimes => 'Paiement en 10 fois',
            self::TwelveTimes => 'Paiement en 12 fois',
        };
    }

    public function getNumberOfPayments(): int
    {
        return match ($this) {
            self::OneTimeCard, self::OneTimeDebit => 1,
            self::ThreeTimes => 3,
            self::FourTimes => 4,
            self::TenTimes => 10,
            self::TwelveTimes => 12,
        };
    }

    public function isOneTime(): bool
    {
        return in_array($this, [self::OneTimeCard, self::OneTimeDebit]);
    }

    public function isMultiPayment(): bool
    {
        return ! $this->isOneTime();
    }

    public static function fromLabel(string $label): ?self
    {
        return match (strtoupper($label)) {
            'CB1XC' => self::OneTimeCard,
            'CB1XD' => self::OneTimeDebit,
            'CB3X' => self::ThreeTimes,
            'CB4X' => self::FourTimes,
            'CB10X' => self::TenTimes,
            'CB12X' => self::TwelveTimes,
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
        return self::OneTimeCard;
    }

    public static function getSelectArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->displayName().' ('.$case->label().')';
        }

        return $result;
    }

    public static function getOneTimeOptions(): array
    {
        $result = [];
        foreach ([self::OneTimeCard, self::OneTimeDebit] as $case) {
            $result[$case->value] = $case->displayName().' ('.$case->label().')';
        }

        return $result;
    }

    public static function getMultiPaymentOptions(): array
    {
        $result = [];
        foreach ([self::ThreeTimes, self::FourTimes, self::TenTimes, self::TwelveTimes] as $case) {
            $result[$case->value] = $case->displayName().' ('.$case->label().')';
        }

        return $result;
    }

    public static function validationRule(): EnumRule
    {
        return new EnumRule(self::class);
    }

    public static function validationRules(): array
    {
        return [
            'required',
            function ($attribute, $value, $fail) {
                if (is_numeric($value) && ! self::tryFrom((int) $value)) {
                    $fail("La valeur de $attribute n'est pas un identifiant de mode de paiement échelonné valide.");
                }

                if (is_string($value) && ! is_numeric($value) && ! self::fromLabel($value)) {
                    $fail("La valeur de $attribute n'est pas un mode de paiement échelonné valide.");
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

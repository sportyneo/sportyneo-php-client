<?php

namespace App\Enums;

enum Psp: string
{
    case RYFT = 'ryft';

    public function label(): string
    {
        return match ($this) {
            self::RYFT => 'Ryft',
        };
    }
}

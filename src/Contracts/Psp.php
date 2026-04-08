<?php

namespace Sportyneo\SDK\Contracts;

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

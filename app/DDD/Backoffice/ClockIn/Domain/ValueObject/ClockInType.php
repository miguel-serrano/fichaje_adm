<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\ValueObject;

enum ClockInType: string
{
    case ENTRY = 'entry';
    case EXIT = 'exit';
    case BREAK_START = 'break_start';
    case BREAK_END = 'break_end';

    public function isEntry(): bool
    {
        return $this === self::ENTRY;
    }

    public function isExit(): bool
    {
        return $this === self::EXIT;
    }

    public function isBreakStart(): bool
    {
        return $this === self::BREAK_START;
    }

    public function isBreakEnd(): bool
    {
        return $this === self::BREAK_END;
    }

    public function label(): string
    {
        return match ($this) {
            self::ENTRY => 'Entrada',
            self::EXIT => 'Salida',
            self::BREAK_START => 'Inicio pausa',
            self::BREAK_END => 'Fin pausa',
        };
    }
}

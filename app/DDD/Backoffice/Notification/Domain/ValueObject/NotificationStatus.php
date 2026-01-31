<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Domain\ValueObject;

enum NotificationStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::SENT => 'Enviada',
            self::FAILED => 'Fallida',
        };
    }
}

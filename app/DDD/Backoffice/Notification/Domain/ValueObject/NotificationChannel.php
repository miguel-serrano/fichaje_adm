<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Domain\ValueObject;

enum NotificationChannel: string
{
    case DATABASE = 'database';   // Notificación en BD (mostrar en UI)
    case EMAIL = 'email';         // Envío por email
    case PUSH = 'push';           // Push notification
    case SMS = 'sms';             // SMS

    public function label(): string
    {
        return match ($this) {
            self::DATABASE => 'Base de datos',
            self::EMAIL => 'Email',
            self::PUSH => 'Push',
            self::SMS => 'SMS',
        };
    }
}

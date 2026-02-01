<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Domain\ValueObject;

enum NotificationChannel: string
{
    case DATABASE = 'database';
    case EMAIL = 'email';
    case PUSH = 'push';
    case SMS = 'sms';

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

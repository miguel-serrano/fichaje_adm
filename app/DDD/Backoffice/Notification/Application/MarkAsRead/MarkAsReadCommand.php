<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\MarkAsRead;

final class MarkAsReadCommand
{
    public function __construct(
        public readonly int $notificationId,
        public readonly int $recipientId,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\MarkAllAsRead;

final class MarkAllAsReadCommand
{
    public function __construct(
        public readonly int $recipientId,
    ) {}
}

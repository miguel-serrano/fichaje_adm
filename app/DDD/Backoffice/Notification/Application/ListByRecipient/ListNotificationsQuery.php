<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\ListByRecipient;

final class ListNotificationsQuery
{
    public function __construct(
        public readonly int $recipientId,
        public readonly bool $onlyUnread = false,
        public readonly int $limit = 50,
    ) {}
}

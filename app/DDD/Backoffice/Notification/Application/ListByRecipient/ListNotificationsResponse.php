<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\ListByRecipient;

final class ListNotificationsResponse
{
    /**
     * @param NotificationItem[] $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $unreadCount,
    ) {}

    public function toArray(): array
    {
        return [
            'items' => array_map(fn ($item) => $item->toArray(), $this->items),
            'unread_count' => $this->unreadCount,
        ];
    }
}

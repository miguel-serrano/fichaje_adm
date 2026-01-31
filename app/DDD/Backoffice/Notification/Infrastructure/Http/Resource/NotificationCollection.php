<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class NotificationCollection extends ResourceCollection
{
    public $collects = NotificationResource::class;

    private int $unreadCount = 0;

    public function withUnreadCount(int $count): self
    {
        $this->unreadCount = $count;
        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'unread_count' => $this->unreadCount,
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\ListByRecipient;

use App\DDD\Backoffice\Notification\Domain\Notification;

final class NotificationItem
{
    private function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly string $typeLabel,
        public readonly string $severity,
        public readonly string $icon,
        public readonly string $title,
        public readonly string $body,
        public readonly bool $isRead,
        public readonly ?array $data,
        public readonly string $createdAt,
        public readonly ?string $readAt,
    ) {}

    public static function fromNotification(Notification $notification): self
    {
        return new self(
            id: $notification->id()->value(),
            type: $notification->type()->value,
            typeLabel: $notification->type()->label(),
            severity: $notification->type()->severity(),
            icon: $notification->type()->icon(),
            title: $notification->title()->value(),
            body: $notification->body()->value(),
            isRead: $notification->isRead(),
            data: $notification->data()?->toArray(),
            createdAt: $notification->createdAt()->format('Y-m-d H:i:s'),
            readAt: $notification->readAt()?->format('Y-m-d H:i:s'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'type_label' => $this->typeLabel,
            'severity' => $this->severity,
            'icon' => $this->icon,
            'title' => $this->title,
            'body' => $this->body,
            'is_read' => $this->isRead,
            'data' => $this->data,
            'created_at' => $this->createdAt,
            'read_at' => $this->readAt,
        ];
    }
}

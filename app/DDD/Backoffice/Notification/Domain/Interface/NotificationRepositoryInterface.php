<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Domain\Interface;

use App\DDD\Backoffice\Notification\Domain\Notification;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationId;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationRecipientId;

interface NotificationRepositoryInterface
{
    public function findById(NotificationId $id): ?Notification;

    public function save(Notification $notification): void;

    public function delete(Notification $notification): void;

    /**
     * @return Notification[]
     */
    public function findByRecipient(NotificationRecipientId $recipientId, int $limit = 50): array;

    /**
     * @return Notification[]
     */
    public function findUnreadByRecipient(NotificationRecipientId $recipientId): array;

    public function countUnreadByRecipient(NotificationRecipientId $recipientId): int;

    public function markAllAsReadForRecipient(NotificationRecipientId $recipientId): void;

    /**
     * @return Notification[]
     */
    public function findPending(int $limit = 100): array;

    public function nextId(): NotificationId;
}

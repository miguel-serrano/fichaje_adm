<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Repository;

use App\DDD\Backoffice\Notification\Domain\Interface\NotificationRepositoryInterface;
use App\DDD\Backoffice\Notification\Domain\Notification;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationId;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationRecipientId;
use App\DDD\Backoffice\Notification\Infrastructure\Mapper\NotificationMapper;

final class NotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(
        private readonly NotificationMapper $mapper,
    ) {}

    public function findById(NotificationId $id): ?Notification
    {
        $model = EloquentNotificationModel::find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function save(Notification $notification): void
    {
        $data = $notification->toPrimitives();

        EloquentNotificationModel::updateOrCreate(
            ['id' => $data['id']],
            [
                'type' => $data['type'],
                'title' => $data['title'],
                'body' => $data['body'],
                'channel' => $data['channel'],
                'status' => $data['status'],
                'recipient_id' => $data['recipient_id'],
                'data' => $data['data'],
                'read_at' => $data['read_at'],
                'sent_at' => $data['sent_at'],
            ],
        );
    }

    public function delete(Notification $notification): void
    {
        EloquentNotificationModel::destroy($notification->id()->value());
    }

    public function findByRecipient(NotificationRecipientId $recipientId, int $limit = 50): array
    {
        return EloquentNotificationModel::where('recipient_id', $recipientId->value())
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->toArray();
    }

    public function findUnreadByRecipient(NotificationRecipientId $recipientId): array
    {
        return EloquentNotificationModel::where('recipient_id', $recipientId->value())
            ->whereNull('read_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->toArray();
    }

    public function countUnreadByRecipient(NotificationRecipientId $recipientId): int
    {
        return EloquentNotificationModel::where('recipient_id', $recipientId->value())
            ->whereNull('read_at')
            ->count();
    }

    public function markAllAsReadForRecipient(NotificationRecipientId $recipientId): void
    {
        EloquentNotificationModel::where('recipient_id', $recipientId->value())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function findPending(int $limit = 100): array
    {
        return EloquentNotificationModel::where('status', 'pending')
            ->orderBy('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->toArray();
    }

    public function nextId(): NotificationId
    {
        $lastId = EloquentNotificationModel::max('id') ?? 0;
        return NotificationId::create($lastId + 1);
    }
}

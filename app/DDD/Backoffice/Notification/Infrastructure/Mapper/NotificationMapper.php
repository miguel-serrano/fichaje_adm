<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Mapper;

use App\DDD\Backoffice\Notification\Domain\Notification;
use App\DDD\Backoffice\Notification\Infrastructure\Repository\EloquentNotificationModel;

final class NotificationMapper
{
    public function toDomain(EloquentNotificationModel $model): Notification
    {
        return Notification::fromPrimitives([
            'id' => $model->id,
            'type' => $model->type,
            'title' => $model->title,
            'body' => $model->body,
            'channel' => $model->channel,
            'status' => $model->status,
            'recipient_id' => $model->recipient_id,
            'data' => $model->data,
            'created_at' => $model->created_at->format('Y-m-d H:i:s'),
            'read_at' => $model->read_at?->format('Y-m-d H:i:s'),
            'sent_at' => $model->sent_at?->format('Y-m-d H:i:s'),
        ]);
    }
}

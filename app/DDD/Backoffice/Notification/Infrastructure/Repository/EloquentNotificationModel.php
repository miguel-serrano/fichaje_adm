<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $type
 * @property string $title
 * @property string $body
 * @property string $channel
 * @property string $status
 * @property int $recipient_id
 * @property array|null $data
 * @property \DateTime|null $read_at
 * @property \DateTime|null $sent_at
 * @property \DateTime $created_at
 */
class EloquentNotificationModel extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'title',
        'body',
        'channel',
        'status',
        'recipient_id',
        'data',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}

<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $event_id
 * @property string $event_name
 * @property string $aggregate_id
 * @property array $payload
 * @property array|null $metadata
 * @property \DateTime $occurred_on
 * @property \DateTime|null $published_at
 */
class EloquentDomainEventModel extends Model
{
    protected $table = 'domain_events';

    protected $primaryKey = 'event_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'event_id',
        'event_name',
        'aggregate_id',
        'payload',
        'metadata',
        'occurred_on',
        'published_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'metadata' => 'array',
        'occurred_on' => 'datetime',
        'published_at' => 'datetime',
    ];
}

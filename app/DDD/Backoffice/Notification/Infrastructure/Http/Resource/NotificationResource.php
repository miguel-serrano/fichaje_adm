<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\DDD\Backoffice\Notification\Domain\Notification $resource
 */
final class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primitives = $this->resource->toPrimitives();

        return [
            'id' => $primitives['id'],
            'type' => $primitives['type'],
            'type_label' => $this->resource->type()->label(),
            'severity' => $this->resource->type()->severity(),
            'icon' => $this->resource->type()->icon(),
            'title' => $primitives['title'],
            'body' => $primitives['body'],
            'channel' => $primitives['channel'],
            'status' => $primitives['status'],
            'is_read' => $this->resource->isRead(),
            'data' => $primitives['data'],
            'timestamps' => [
                'created_at' => $primitives['created_at'],
                'read_at' => $primitives['read_at'],
                'sent_at' => $primitives['sent_at'],
                'time_ago' => $this->getTimeAgo($primitives['created_at']),
            ],
        ];
    }

    private function getTimeAgo(string $timestamp): string
    {
        $dt = new \DateTimeImmutable($timestamp);
        $now = new \DateTimeImmutable();
        $diff = $now->diff($dt);

        if ($diff->days === 0) {
            if ($diff->h === 0) {
                if ($diff->i === 0) {
                    return 'Ahora mismo';
                }
                return sprintf('Hace %d min', $diff->i);
            }
            return sprintf('Hace %d h', $diff->h);
        }

        if ($diff->days === 1) {
            return 'Ayer';
        }

        if ($diff->days < 7) {
            return sprintf('Hace %d días', $diff->days);
        }

        return $dt->format('d/m/Y');
    }
}

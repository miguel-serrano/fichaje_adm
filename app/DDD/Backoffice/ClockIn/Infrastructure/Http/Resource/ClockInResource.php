<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\DDD\Backoffice\ClockIn\Domain\ClockIn $resource
 */
final class ClockInResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primitives = $this->resource->toPrimitives();

        return [
            'id' => $primitives['id'],
            'employee_id' => $primitives['employee_id'],
            'type' => $primitives['type'],
            'type_label' => $this->getTypeLabel($primitives['type']),
            'type_icon' => $this->getTypeIcon($primitives['type']),
            'timestamp' => $primitives['timestamp'],
            'timestamp_formatted' => $this->formatTimestamp($primitives['timestamp']),
            'location' => [
                'latitude' => $primitives['latitude'],
                'longitude' => $primitives['longitude'],
                'google_maps_url' => $this->getGoogleMapsUrl(
                    $primitives['latitude'],
                    $primitives['longitude']
                ),
            ],
            'workplace_id' => $primitives['workplace_id'],
            'notes' => $primitives['notes'],
            'created_at' => $primitives['created_at'],
        ];
    }

    private function getTypeLabel(string $type): string
    {
        return match ($type) {
            'entry' => 'Entrada',
            'exit' => 'Salida',
            'break_start' => 'Inicio descanso',
            'break_end' => 'Fin descanso',
            default => $type,
        };
    }

    private function getTypeIcon(string $type): string
    {
        return match ($type) {
            'entry' => 'login',
            'exit' => 'logout',
            'break_start' => 'coffee',
            'break_end' => 'play',
            default => 'clock',
        };
    }

    private function formatTimestamp(string $timestamp): array
    {
        $dt = new \DateTimeImmutable($timestamp);

        return [
            'date' => $dt->format('Y-m-d'),
            'time' => $dt->format('H:i'),
            'day_name' => $this->getDayName((int) $dt->format('N')),
            'relative' => $this->getRelativeTime($dt),
        ];
    }

    private function getDayName(int $dayNumber): string
    {
        $days = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        return $days[$dayNumber] ?? '';
    }

    private function getRelativeTime(\DateTimeImmutable $dt): string
    {
        $now = new \DateTimeImmutable();
        $diff = $now->diff($dt);

        if ($diff->days === 0) {
            return 'Hoy';
        }

        if ($diff->days === 1 && $dt < $now) {
            return 'Ayer';
        }

        if ($diff->days < 7) {
            return "Hace {$diff->days} días";
        }

        return $dt->format('d/m/Y');
    }

    private function getGoogleMapsUrl(?float $lat, ?float $lng): ?string
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        return sprintf(
            'https://www.google.com/maps?q=%f,%f',
            $lat,
            $lng
        );
    }
}

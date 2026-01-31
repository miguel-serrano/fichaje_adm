<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\DDD\Backoffice\Employee\Domain\Entity\EmployeePlanification $resource
 */
final class EmployeePlanificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primitives = $this->resource->toPrimitives();

        return [
            'id' => $primitives['id'],
            'total_week_hours' => $this->resource->totalWeekHours(),
            'week_schedule' => $primitives['week_schedule'],
            'formatted_schedule' => $this->formatSchedule($primitives['week_schedule']),
        ];
    }

    private function formatSchedule(array $weekSchedule): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $dayLabels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        $formatted = [];

        foreach ($days as $index => $day) {
            $daySchedule = $weekSchedule[$day] ?? null;

            if ($daySchedule === null || ($daySchedule['is_day_off'] ?? true)) {
                $formatted[] = [
                    'day' => $dayLabels[$index],
                    'is_day_off' => true,
                    'slots' => [],
                ];
                continue;
            }

            $slots = array_map(function ($slot) {
                return sprintf('%s - %s', $slot['start'], $slot['end']);
            }, $daySchedule['slots'] ?? []);

            $formatted[] = [
                'day' => $dayLabels[$index],
                'is_day_off' => false,
                'slots' => $slots,
                'summary' => implode(', ', $slots),
            ];
        }

        return $formatted;
    }
}

<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\DDD\Backoffice\Employee\Domain\Entity\EmployeeContract $resource
 */
final class EmployeeContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primitives = $this->resource->toPrimitives();

        return [
            'id' => $primitives['id'],
            'type' => $primitives['type'],
            'type_label' => $this->getTypeLabel($primitives['type']),
            'start_date' => $primitives['start_date'],
            'end_date' => $primitives['end_date'],
            'hours_per_week' => $primitives['hours_per_week'],
            'is_active' => $this->resource->isActive(),
            'is_expiring_soon' => $this->resource->isExpiringSoon(),
            'days_until_expiration' => $this->resource->daysUntilExpiration(),
        ];
    }

    private function getTypeLabel(string $type): string
    {
        return match ($type) {
            'full_time' => 'Tiempo completo',
            'part_time' => 'Tiempo parcial',
            'temporary' => 'Temporal',
            'internship' => 'Prácticas',
            default => $type,
        };
    }
}

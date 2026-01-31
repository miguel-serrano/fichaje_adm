<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\DDD\Backoffice\Employee\Domain\Employee $resource
 */
final class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primitives = $this->resource->toPrimitives();

        return [
            'id' => $primitives['id'],
            'name' => $primitives['name'],
            'last_name' => $primitives['last_name'],
            'full_name' => $this->resource->fullName(),
            'email' => $primitives['email'],
            'phone' => $primitives['phone'],
            'nid' => $primitives['nid'],
            'code' => $primitives['code'],
            'is_active' => $primitives['is_active'],
            'contract' => $this->when(
                $this->resource->hasContract(),
                fn () => new EmployeeContractResource($this->resource->contract())
            ),
            'planification' => $this->when(
                $this->resource->hasPlanification(),
                fn () => new EmployeePlanificationResource($this->resource->planification())
            ),
            'workplace_ids' => $primitives['workplace_ids'],
            'links' => [
                'self' => route('backoffice.employees.show', $primitives['id']),
                'clock_ins' => route('backoffice.clock-ins.index', ['employee_id' => $primitives['id']]),
            ],
        ];
    }
}

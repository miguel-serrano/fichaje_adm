<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class WorkplaceCollection extends ResourceCollection
{
    public $collects = WorkplaceResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
            ],
        ];
    }
}

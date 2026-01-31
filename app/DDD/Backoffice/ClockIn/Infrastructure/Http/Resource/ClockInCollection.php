<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ClockInCollection extends ResourceCollection
{
    public $collects = ClockInResource::class;

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

<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Controller;

use App\DDD\Shared\Application\Event\SearchEventsQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SearchEventsController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $response = $this->queryBus->ask(new SearchEventsQuery(
            eventName: $request->query('event_name'),
            aggregateId: $request->query('aggregate_id'),
            from: $request->query('from'),
            to: $request->query('to'),
            payloadSearch: $request->query('q'),
            limit: (int) $request->query('limit', 100),
            offset: (int) $request->query('offset', 0),
        ));

        return response()->json($response->toArray());
    }
}

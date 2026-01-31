<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Workplace\Application\ListAll\ListWorkplacesQuery;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ListWorkplacesController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        $onlyActive = $request->boolean('only_active', true);

        $response = $this->queryBus->ask(new ListWorkplacesQuery(
            activeUserId: $this->activeUserId(),
            onlyActive: $onlyActive,
        ));

        return Inertia::render('Backoffice/Workplaces/Index', [
            'workplaces' => $response->toArray(),
            'filters' => [
                'only_active' => $onlyActive,
            ],
        ]);
    }
}

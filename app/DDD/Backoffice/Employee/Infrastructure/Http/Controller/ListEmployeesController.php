<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\ListAll\ListEmployeesQuery;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ListEmployeesController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        $onlyActive = $request->boolean('only_active', true);

        $response = $this->queryBus->ask(new ListEmployeesQuery(
            activeUserId: $this->activeUserId(),
            onlyActive: $onlyActive,
        ));

        return Inertia::render('Backoffice/Employees/Index', [
            'employees' => $response->toArray(),
            'filters' => [
                'only_active' => $onlyActive,
            ],
        ]);
    }
}

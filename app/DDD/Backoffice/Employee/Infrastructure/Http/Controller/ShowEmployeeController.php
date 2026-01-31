<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\Find\FindEmployeeQuery;
use App\DDD\Backoffice\Workplace\Application\ListAll\ListWorkplacesQuery;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Inertia\Inertia;
use Inertia\Response;

final class ShowEmployeeController extends BaseController
{
    public function __invoke(int $id): Response
    {
        $response = $this->queryBus->ask(new FindEmployeeQuery(
            activeUserId: $this->activeUserId(),
            employeeId: $id,
        ));

        $workplacesResponse = $this->queryBus->ask(new ListWorkplacesQuery(
            activeUserId: $this->activeUserId(),
            onlyActive: true,
        ));

        return Inertia::render('Backoffice/Employees/Show', [
            'employee' => $response->toArray(),
            'workplaces' => $workplacesResponse->toArray(),
        ]);
    }
}

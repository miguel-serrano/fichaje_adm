<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Workplace\Application\Find\FindWorkplaceQuery;
use App\DDD\Backoffice\Workplace\Infrastructure\Repository\EloquentWorkplaceModel;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Inertia\Inertia;
use Inertia\Response;

final class ShowWorkplaceController extends BaseController
{
    public function __invoke(int $id): Response
    {
        $response = $this->queryBus->ask(new FindWorkplaceQuery(
            activeUserId: $this->activeUserId(),
            workplaceId: $id,
        ));

        $employees = EloquentWorkplaceModel::find($id)
            ?->employees()
            ->select(['employees.id', 'employees.name', 'employees.last_name', 'employees.email', 'employees.is_active'])
            ->get()
            ->toArray() ?? [];

        return Inertia::render('Backoffice/Workplaces/Show', [
            'workplace' => $response->toArray(),
            'employees' => $employees,
        ]);
    }
}

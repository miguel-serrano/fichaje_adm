<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\Workplace\UpdateEmployeeWorkplacesCommand;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UpdateEmployeeWorkplacesController extends BaseController
{
    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'workplace_ids' => ['present', 'array'],
            'workplace_ids.*' => ['integer', 'exists:workplaces,id'],
        ]);

        $this->commandBus->dispatch(new UpdateEmployeeWorkplacesCommand(
            activeUserId: $this->activeUserId(),
            employeeId: $id,
            workplaceIds: array_map('intval', $validated['workplace_ids']),
        ));

        return redirect()
            ->route('backoffice.employees.show', $id)
            ->with('success', 'Centros de trabajo actualizados.');
    }
}

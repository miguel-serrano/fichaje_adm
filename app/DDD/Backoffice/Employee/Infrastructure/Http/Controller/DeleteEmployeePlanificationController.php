<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\Planification\Delete\DeleteEmployeePlanificationCommand;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class DeleteEmployeePlanificationController extends BaseController
{
    public function __invoke(int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteEmployeePlanificationCommand(
            activeUserId: $this->activeUserId(),
            employeeId: $id,
        ));

        return redirect()
            ->route('backoffice.employees.show', $id)
            ->with('success', 'Horario semanal eliminado correctamente.');
    }
}

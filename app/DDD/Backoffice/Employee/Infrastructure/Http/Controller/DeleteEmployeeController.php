<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\Delete\DeleteEmployeeCommand;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class DeleteEmployeeController extends BaseController
{
    public function __invoke(int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteEmployeeCommand(
            activeUserId: $this->activeUserId(),
            employeeId: $id,
        ));

        return redirect()
            ->route('backoffice.employees.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}

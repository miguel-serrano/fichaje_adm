<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\Update\UpdateEmployeeCommand;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Request\UpdateEmployeeRequest;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class UpdateEmployeeController extends BaseController
{
    public function __invoke(UpdateEmployeeRequest $request, int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new UpdateEmployeeCommand(
            activeUserId: $this->activeUserId(),
            employeeId: $id,
            name: $request->string('name')->toString(),
            lastName: $request->string('last_name')->toString(),
            email: $request->string('email')->toString(),
            phone: $request->filled('phone') ? $request->string('phone')->toString() : null,
            nid: $request->filled('nid') ? $request->string('nid')->toString() : null,
            code: $request->filled('code') ? $request->string('code')->toString() : null,
            isActive: $request->boolean('is_active', true),
        ));

        return redirect()
            ->route('backoffice.employees.show', $id)
            ->with('success', 'Empleado actualizado correctamente.');
    }
}

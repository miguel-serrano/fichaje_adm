<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\Create\CreateEmployeeCommand;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Request\CreateEmployeeRequest;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class CreateEmployeeController extends BaseController
{
    public function __invoke(CreateEmployeeRequest $request): RedirectResponse
    {
        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->activeUserId(),
            name: $request->string('name')->toString(),
            lastName: $request->string('last_name')->toString(),
            email: $request->string('email')->toString(),
            phone: $request->filled('phone') ? $request->string('phone')->toString() : null,
            nid: $request->filled('nid') ? $request->string('nid')->toString() : null,
            code: $request->filled('code') ? $request->string('code')->toString() : null,
        ));

        return redirect()
            ->route('backoffice.employees.index')
            ->with('success', 'Empleado creado correctamente.');
    }
}

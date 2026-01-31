<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Http\Controller;

use App\DDD\Backoffice\ClockIn\Application\Create\CreateClockInCommand;
use App\DDD\Backoffice\ClockIn\Infrastructure\Http\Request\CreateClockInRequest;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class CreateClockInController extends BaseController
{
    public function __invoke(CreateClockInRequest $request): RedirectResponse
    {
        $this->commandBus->dispatch(new CreateClockInCommand(
            activeUserId: $this->activeUserId(),
            employeeId: $request->integer('employee_id'),
            type: $request->string('type')->toString(),
            timestamp: $request->string('timestamp')->toString(),
            latitude: $request->float('latitude'),
            longitude: $request->float('longitude'),
            workplaceId: $request->filled('workplace_id') ? $request->integer('workplace_id') : null,
            notes: $request->filled('notes') ? $request->string('notes')->toString() : null,
        ));

        return redirect()
            ->route('backoffice.clock-ins.index')
            ->with('success', 'Fichaje registrado correctamente.');
    }
}

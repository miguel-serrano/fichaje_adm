<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\Contract\Upsert\UpsertEmployeeContractCommand;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UpsertEmployeeContractController extends BaseController
{
    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|in:indefinite,temporary,part_time,internship,freelance',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'hours_per_week' => 'required|numeric|min:1|max:168',
        ]);

        $this->commandBus->dispatch(new UpsertEmployeeContractCommand(
            activeUserId: $this->activeUserId(),
            employeeId: $id,
            type: $request->string('type')->toString(),
            startDate: $request->string('start_date')->toString(),
            endDate: $request->filled('end_date') ? $request->string('end_date')->toString() : null,
            hoursPerWeek: $request->float('hours_per_week'),
        ));

        return redirect()
            ->route('backoffice.employees.show', $id)
            ->with('success', 'Contrato actualizado correctamente.');
    }
}

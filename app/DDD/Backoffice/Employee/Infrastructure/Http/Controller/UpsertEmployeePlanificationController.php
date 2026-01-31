<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Employee\Application\Planification\Upsert\UpsertEmployeePlanificationCommand;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UpsertEmployeePlanificationController extends BaseController
{
    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'week_schedule' => 'required|array',
            'week_schedule.*' => 'required|array',
            'week_schedule.*.is_day_off' => 'required|boolean',
            'week_schedule.*.slots' => 'array',
            'week_schedule.*.slots.*.start_time' => 'required|string|date_format:H:i',
            'week_schedule.*.slots.*.end_time' => 'required|string|date_format:H:i',
        ]);

        $this->commandBus->dispatch(new UpsertEmployeePlanificationCommand(
            activeUserId: $this->activeUserId(),
            employeeId: $id,
            weekSchedule: $request->input('week_schedule'),
        ));

        return redirect()
            ->route('backoffice.employees.show', $id)
            ->with('success', 'Horario semanal actualizado correctamente.');
    }
}

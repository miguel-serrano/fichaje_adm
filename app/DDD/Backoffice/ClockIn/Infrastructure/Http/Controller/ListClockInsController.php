<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Http\Controller;

use App\DDD\Backoffice\ClockIn\Application\ListByEmployee\ListClockInsByEmployeeQuery;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class ListClockInsController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        $employeeId = $request->input('employee_id');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $clockIns = [];

        if ($employeeId) {
            $response = $this->queryBus->ask(new ListClockInsByEmployeeQuery(
                activeUserId: $this->activeUserId(),
                employeeId: (int) $employeeId,
                startDate: $startDate,
                endDate: $endDate,
            ));

            $clockIns = $response->toArray();
        } else {
            $clockIns = DB::table('clock_ins')
                ->join('employees', 'clock_ins.employee_id', '=', 'employees.id')
                ->leftJoin('workplaces', 'clock_ins.workplace_id', '=', 'workplaces.id')
                ->select(
                    'clock_ins.*',
                    'employees.name as employee_name',
                    'employees.last_name as employee_last_name',
                    'workplaces.name as workplace_name'
                )
                ->orderByDesc('clock_ins.timestamp')
                ->limit(50)
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'employee_id' => $c->employee_id,
                    'employee_name' => $c->employee_name . ' ' . $c->employee_last_name,
                    'type' => $c->type,
                    'timestamp' => $c->timestamp,
                    'workplace_name' => $c->workplace_name,
                    'latitude' => $c->latitude,
                    'longitude' => $c->longitude,
                ])
                ->toArray();
        }

        $employees = DB::table('employees')
            ->where('is_active', true)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->name . ' ' . $e->last_name,
            ]);

        $workplaces = DB::table('workplaces')
            ->where('is_active', true)
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'name' => $w->name,
            ]);

        return Inertia::render('Backoffice/ClockIns/Index', [
            'clockIns' => $clockIns,
            'employees' => $employees,
            'workplaces' => $workplaces,
            'filters' => [
                'employee_id' => $employeeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}

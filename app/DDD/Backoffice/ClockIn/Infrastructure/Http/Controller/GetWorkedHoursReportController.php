<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Http\Controller;

use App\DDD\Backoffice\ClockIn\Application\GetWorkedHoursReport\GetWorkedHoursReportQuery;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class GetWorkedHoursReportController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        $employeeId = $request->input('employee_id');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $report = null;

        if ($employeeId) {
            $response = $this->queryBus->ask(new GetWorkedHoursReportQuery(
                activeUserId: $this->activeUserId(),
                employeeId: (int) $employeeId,
                startDate: $startDate,
                endDate: $endDate,
                compareWithPlanification: true,
            ));

            $report = $response->toArray();
        }

        $employees = DB::table('employees')
            ->where('is_active', true)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->name . ' ' . $e->last_name,
            ]);

        return Inertia::render('Backoffice/ClockIns/Report', [
            'report' => $report,
            'employees' => $employees,
            'filters' => [
                'employee_id' => $employeeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}

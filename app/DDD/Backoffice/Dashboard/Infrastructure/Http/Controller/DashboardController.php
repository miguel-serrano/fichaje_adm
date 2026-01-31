<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Dashboard\Infrastructure\Http\Controller;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $userId = auth()->id();

        return Inertia::render('Backoffice/Dashboard', [
            'stats' => [
                'activeEmployees' => DB::table('employees')->where('is_active', true)->count(),
                'clockInsToday' => DB::table('clock_ins')->whereDate('timestamp', today())->count(),
                'workplaces' => DB::table('workplaces')->where('is_active', true)->count(),
                'incidents' => $userId
                    ? DB::table('notifications')->where('recipient_id', $userId)->whereNull('read_at')->count()
                    : 0,
            ],
        ]);
    }
}

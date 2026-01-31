<?php

use App\DDD\Backoffice\ClockIn\Infrastructure\Http\Controller\CreateClockInController;
use App\DDD\Backoffice\ClockIn\Infrastructure\Http\Controller\GetWorkedHoursReportController;
use App\DDD\Backoffice\ClockIn\Infrastructure\Http\Controller\ListClockInsController;
use App\DDD\Backoffice\Dashboard\Infrastructure\Http\Controller\DashboardController;
use App\DDD\Shared\Infrastructure\Http\Controller\ListDomainEventsController;
use App\DDD\Shared\Infrastructure\Http\Controller\SyncEventsToElasticsearchController;
use App\DDD\Backoffice\Notification\Infrastructure\Http\Controller\ListNotificationsController;
use App\DDD\Backoffice\Notification\Infrastructure\Http\Controller\MarkAllNotificationsAsReadController;
use App\DDD\Backoffice\Notification\Infrastructure\Http\Controller\MarkNotificationAsReadController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\CreateEmployeeController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\DeleteEmployeeController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\DeleteEmployeeContractController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\DeleteEmployeePlanificationController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\ListEmployeesController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\ShowEmployeeController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\UpdateEmployeeController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\UpsertEmployeeContractController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\UpdateEmployeeWorkplacesController;
use App\DDD\Backoffice\Employee\Infrastructure\Http\Controller\UpsertEmployeePlanificationController;
use App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller\CreateWorkplaceController;
use App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller\DeleteWorkplaceController;
use App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller\ListWorkplacesController;
use App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller\ShowWorkplaceController;
use App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller\UpdateWorkplaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('backoffice')->name('backoffice.')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', DashboardController::class)->name('dashboard');

    // Employees
    Route::get('/employees', ListEmployeesController::class)->name('employees.index');
    Route::post('/employees', CreateEmployeeController::class)->name('employees.store');
    Route::get('/employees/{id}', ShowEmployeeController::class)->name('employees.show');
    Route::put('/employees/{id}', UpdateEmployeeController::class)->name('employees.update');
    Route::delete('/employees/{id}', DeleteEmployeeController::class)->name('employees.destroy');
    Route::put('/employees/{id}/contract', UpsertEmployeeContractController::class)->name('employees.contract.upsert');
    Route::delete('/employees/{id}/contract', DeleteEmployeeContractController::class)->name('employees.contract.destroy');
    Route::put('/employees/{id}/planification', UpsertEmployeePlanificationController::class)->name('employees.planification.upsert');
    Route::delete('/employees/{id}/planification', DeleteEmployeePlanificationController::class)->name('employees.planification.destroy');
    Route::put('/employees/{id}/workplaces', UpdateEmployeeWorkplacesController::class)->name('employees.workplaces.update');

    // Workplaces
    Route::get('/workplaces', ListWorkplacesController::class)->name('workplaces.index');
    Route::post('/workplaces', CreateWorkplaceController::class)->name('workplaces.store');
    Route::get('/workplaces/{id}', ShowWorkplaceController::class)->name('workplaces.show');
    Route::put('/workplaces/{id}', UpdateWorkplaceController::class)->name('workplaces.update');
    Route::delete('/workplaces/{id}', DeleteWorkplaceController::class)->name('workplaces.destroy');

    // Clock-ins
    Route::get('/clock-ins', ListClockInsController::class)->name('clock-ins.index');
    Route::post('/clock-ins', CreateClockInController::class)->name('clock-ins.store');
    Route::get('/clock-ins/report', GetWorkedHoursReportController::class)->name('clock-ins.report');

    // Notifications
    Route::get('/notifications', ListNotificationsController::class)->name('notifications.index');
    Route::post('/notifications/{id}/read', MarkNotificationAsReadController::class)->name('notifications.read');
    Route::post('/notifications/read-all', MarkAllNotificationsAsReadController::class)->name('notifications.read-all');

    // Events
    Route::get('/events', ListDomainEventsController::class)->name('events.index');
    Route::post('/events/sync', SyncEventsToElasticsearchController::class)->name('events.sync');
});

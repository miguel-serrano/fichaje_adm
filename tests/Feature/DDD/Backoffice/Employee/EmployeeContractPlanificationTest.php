<?php

declare(strict_types=1);

namespace Tests\Feature\DDD\Backoffice\Employee;

use App\DDD\Backoffice\Employee\Application\Contract\Delete\DeleteEmployeeContractCommand;
use App\DDD\Backoffice\Employee\Application\Contract\Upsert\UpsertEmployeeContractCommand;
use App\DDD\Backoffice\Employee\Application\Create\CreateEmployeeCommand;
use App\DDD\Backoffice\Employee\Application\Find\FindEmployeeQuery;
use App\DDD\Backoffice\Employee\Application\Planification\Delete\DeleteEmployeePlanificationCommand;
use App\DDD\Backoffice\Employee\Application\Planification\Upsert\UpsertEmployeePlanificationCommand;
use App\DDD\Shared\Infrastructure\Bus\CommandBusInterface;
use App\DDD\Shared\Infrastructure\Bus\QueryBusInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EmployeeContractPlanificationTest extends TestCase
{
    use RefreshDatabase;

    private CommandBusInterface $commandBus;
    private QueryBusInterface $queryBus;
    private int $userId;
    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->app->make(CommandBusInterface::class);
        $this->queryBus = $this->app->make(QueryBusInterface::class);

        $roleId = \DB::table('roles')->insertGetId([
            'name' => 'super_admin',
            'display_name' => 'Super Admin',
            'is_super_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::factory()->create(['role_id' => $roleId]);
        $this->userId = $user->id;

        // Create an employee to work with
        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->userId,
            name: 'Test',
            lastName: 'Employee',
            email: 'test-contract@example.com',
        ));

        $this->employeeId = \DB::table('employees')
            ->where('email', 'test-contract@example.com')
            ->value('id');
    }

    public function test_create_employee_contract(): void
    {
        $this->commandBus->dispatch(new UpsertEmployeeContractCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
            type: 'indefinite',
            startDate: '2025-01-01',
            endDate: null,
            hoursPerWeek: 40,
        ));

        $this->assertDatabaseHas('employee_contracts', [
            'employee_id' => $this->employeeId,
            'type' => 'indefinite',
            'hours_per_week' => 40,
        ]);
    }

    public function test_update_employee_contract(): void
    {
        $this->commandBus->dispatch(new UpsertEmployeeContractCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
            type: 'indefinite',
            startDate: '2025-01-01',
            endDate: null,
            hoursPerWeek: 40,
        ));

        $this->commandBus->dispatch(new UpsertEmployeeContractCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
            type: 'temporary',
            startDate: '2025-06-01',
            endDate: '2025-12-31',
            hoursPerWeek: 20,
        ));

        $this->assertDatabaseHas('employee_contracts', [
            'employee_id' => $this->employeeId,
            'type' => 'temporary',
            'hours_per_week' => 20,
        ]);

        // Should only have one contract per employee
        $this->assertEquals(1, \DB::table('employee_contracts')
            ->where('employee_id', $this->employeeId)
            ->count());
    }

    public function test_delete_employee_contract(): void
    {
        $this->commandBus->dispatch(new UpsertEmployeeContractCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
            type: 'indefinite',
            startDate: '2025-01-01',
            endDate: null,
            hoursPerWeek: 40,
        ));

        $this->commandBus->dispatch(new DeleteEmployeeContractCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
        ));

        $this->assertDatabaseMissing('employee_contracts', [
            'employee_id' => $this->employeeId,
        ]);
    }

    public function test_create_employee_planification(): void
    {
        $weekSchedule = [
            1 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00'], ['start_time' => '15:00', 'end_time' => '18:00']]],
            2 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00'], ['start_time' => '15:00', 'end_time' => '18:00']]],
            3 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00'], ['start_time' => '15:00', 'end_time' => '18:00']]],
            4 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00'], ['start_time' => '15:00', 'end_time' => '18:00']]],
            5 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00'], ['start_time' => '15:00', 'end_time' => '18:00']]],
            6 => ['is_day_off' => true, 'slots' => []],
            7 => ['is_day_off' => true, 'slots' => []],
        ];

        $this->commandBus->dispatch(new UpsertEmployeePlanificationCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
            weekSchedule: $weekSchedule,
        ));

        $this->assertDatabaseHas('employee_planifications', [
            'employee_id' => $this->employeeId,
            'total_week_hours' => 40,
        ]);
    }

    public function test_update_employee_planification(): void
    {
        $weekSchedule = [
            1 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00']]],
            2 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00']]],
            3 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00']]],
            4 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00']]],
            5 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00']]],
            6 => ['is_day_off' => true, 'slots' => []],
            7 => ['is_day_off' => true, 'slots' => []],
        ];

        $this->commandBus->dispatch(new UpsertEmployeePlanificationCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
            weekSchedule: $weekSchedule,
        ));

        $this->assertDatabaseHas('employee_planifications', [
            'employee_id' => $this->employeeId,
            'total_week_hours' => 25,
        ]);

        // Update to full-time
        $weekSchedule[1]['slots'][] = ['start_time' => '15:00', 'end_time' => '18:00'];
        $weekSchedule[2]['slots'][] = ['start_time' => '15:00', 'end_time' => '18:00'];
        $weekSchedule[3]['slots'][] = ['start_time' => '15:00', 'end_time' => '18:00'];
        $weekSchedule[4]['slots'][] = ['start_time' => '15:00', 'end_time' => '18:00'];
        $weekSchedule[5]['slots'][] = ['start_time' => '15:00', 'end_time' => '18:00'];

        $this->commandBus->dispatch(new UpsertEmployeePlanificationCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
            weekSchedule: $weekSchedule,
        ));

        $this->assertDatabaseHas('employee_planifications', [
            'employee_id' => $this->employeeId,
            'total_week_hours' => 40,
        ]);

        // Should only have one planification per employee
        $this->assertEquals(1, \DB::table('employee_planifications')
            ->where('employee_id', $this->employeeId)
            ->count());
    }

    public function test_delete_employee_planification(): void
    {
        $weekSchedule = [
            1 => ['is_day_off' => false, 'slots' => [['start_time' => '09:00', 'end_time' => '14:00']]],
            2 => ['is_day_off' => true, 'slots' => []],
            3 => ['is_day_off' => true, 'slots' => []],
            4 => ['is_day_off' => true, 'slots' => []],
            5 => ['is_day_off' => true, 'slots' => []],
            6 => ['is_day_off' => true, 'slots' => []],
            7 => ['is_day_off' => true, 'slots' => []],
        ];

        $this->commandBus->dispatch(new UpsertEmployeePlanificationCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
            weekSchedule: $weekSchedule,
        ));

        $this->commandBus->dispatch(new DeleteEmployeePlanificationCommand(
            activeUserId: $this->userId,
            employeeId: $this->employeeId,
        ));

        $this->assertDatabaseMissing('employee_planifications', [
            'employee_id' => $this->employeeId,
        ]);
    }
}

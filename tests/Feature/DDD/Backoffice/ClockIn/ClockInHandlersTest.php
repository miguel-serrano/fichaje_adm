<?php

declare(strict_types=1);

namespace Tests\Feature\DDD\Backoffice\ClockIn;

use App\DDD\Backoffice\ClockIn\Application\Create\CreateClockInCommand;
use App\DDD\Backoffice\ClockIn\Application\ListByEmployee\ListClockInsByEmployeeQuery;
use App\DDD\Backoffice\ClockIn\Application\GetWorkedHoursReport\GetWorkedHoursReportQuery;
use App\DDD\Backoffice\Employee\Application\Create\CreateEmployeeCommand;
use App\DDD\Backoffice\Workplace\Application\Create\CreateWorkplaceCommand;
use App\DDD\Shared\Infrastructure\Bus\CommandBusInterface;
use App\DDD\Shared\Infrastructure\Bus\QueryBusInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ClockInHandlersTest extends TestCase
{
    use RefreshDatabase;

    private CommandBusInterface $commandBus;
    private QueryBusInterface $queryBus;
    private int $userId;

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
    }

    private function createEmployee(string $email = 'emp@example.com'): int
    {
        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->userId,
            name: 'Test',
            lastName: 'Employee',
            email: $email,
        ));

        return \DB::table('employees')->where('email', $email)->value('id');
    }

    private function createWorkplace(
        string $name = 'Oficina Test',
        ?float $latitude = null,
        ?float $longitude = null,
        ?int $radius = null,
    ): int {
        $this->commandBus->dispatch(new CreateWorkplaceCommand(
            activeUserId: $this->userId,
            name: $name,
            latitude: $latitude,
            longitude: $longitude,
            radius: $radius,
        ));

        return \DB::table('workplaces')->where('name', $name)->value('id');
    }

    public function test_create_clock_in(): void
    {
        $employeeId = $this->createEmployee();

        $this->commandBus->dispatch(new CreateClockInCommand(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            type: 'entry',
            timestamp: now()->toDateTimeString(),
            latitude: 40.4168,
            longitude: -3.7038,
        ));

        $this->assertDatabaseHas('clock_ins', [
            'employee_id' => $employeeId,
            'type' => 'entry',
        ]);
    }

    public function test_create_clock_in_with_valid_geofence(): void
    {
        $employeeId = $this->createEmployee();
        $workplaceId = $this->createWorkplace(
            name: 'Oficina Geofence',
            latitude: 40.4168,
            longitude: -3.7038,
            radius: 500,
        );

        $this->commandBus->dispatch(new CreateClockInCommand(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            type: 'entry',
            timestamp: now()->toDateTimeString(),
            latitude: 40.4168,
            longitude: -3.7038,
            workplaceId: $workplaceId,
        ));

        $this->assertDatabaseHas('clock_ins', [
            'employee_id' => $employeeId,
            'workplace_id' => $workplaceId,
            'type' => 'entry',
        ]);
    }

    public function test_create_clock_in_with_geofence_violation(): void
    {
        $employeeId = $this->createEmployee();
        $workplaceId = $this->createWorkplace(
            name: 'Oficina Strict',
            latitude: 40.4168,
            longitude: -3.7038,
            radius: 100,
        );

        // Clock in from far away - should still create but trigger violation event
        $this->commandBus->dispatch(new CreateClockInCommand(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            type: 'entry',
            timestamp: now()->toDateTimeString(),
            latitude: 41.3851, // Barcelona coordinates - far from Madrid
            longitude: 2.1734,
            workplaceId: $workplaceId,
        ));

        // Clock in should still be created (soft check)
        $this->assertDatabaseHas('clock_ins', [
            'employee_id' => $employeeId,
            'workplace_id' => $workplaceId,
            'type' => 'entry',
        ]);
    }

    public function test_list_clock_ins_by_employee(): void
    {
        $employeeId = $this->createEmployee();

        $this->commandBus->dispatch(new CreateClockInCommand(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            type: 'entry',
            timestamp: now()->toDateTimeString(),
            latitude: 40.4168,
            longitude: -3.7038,
        ));

        $response = $this->queryBus->ask(new ListClockInsByEmployeeQuery(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            startDate: now()->startOfMonth()->toDateString(),
            endDate: now()->endOfMonth()->toDateString(),
        ));

        $items = $response->toArray();

        $this->assertGreaterThanOrEqual(1, count($items));
    }

    public function test_worked_hours_report(): void
    {
        $employeeId = $this->createEmployee();

        // Create entry
        $this->commandBus->dispatch(new CreateClockInCommand(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            type: 'entry',
            timestamp: now()->setTime(9, 0)->toDateTimeString(),
            latitude: 0,
            longitude: 0,
        ));

        // Create exit
        $this->commandBus->dispatch(new CreateClockInCommand(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            type: 'exit',
            timestamp: now()->setTime(17, 0)->toDateTimeString(),
            latitude: 0,
            longitude: 0,
        ));

        $response = $this->queryBus->ask(new GetWorkedHoursReportQuery(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            startDate: now()->startOfMonth()->toDateString(),
            endDate: now()->endOfMonth()->toDateString(),
            compareWithPlanification: false,
        ));

        $report = $response->toArray();

        $this->assertNotNull($report);
        $this->assertArrayHasKey('worked_hours', $report);
    }
}

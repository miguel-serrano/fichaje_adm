<?php

declare(strict_types=1);

namespace Tests\Feature\DDD\Backoffice\Employee;

use App\DDD\Backoffice\Employee\Application\Create\CreateEmployeeCommand;
use App\DDD\Backoffice\Employee\Application\Delete\DeleteEmployeeCommand;
use App\DDD\Backoffice\Employee\Application\Find\FindEmployeeQuery;
use App\DDD\Backoffice\Employee\Application\ListAll\ListEmployeesQuery;
use App\DDD\Backoffice\Employee\Application\Update\UpdateEmployeeCommand;
use App\DDD\Shared\Infrastructure\Bus\CommandBusInterface;
use App\DDD\Shared\Infrastructure\Bus\QueryBusInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EmployeeHandlersTest extends TestCase
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

    public function test_create_employee(): void
    {
        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->userId,
            name: 'Juan',
            lastName: 'García',
            email: 'juan@example.com',
            phone: '666111222',
            nid: '12345678Z',
            code: 'EMP001',
        ));

        $this->assertDatabaseHas('employees', [
            'name' => 'Juan',
            'last_name' => 'García',
            'email' => 'juan@example.com',
            'phone' => '666111222',
            'nid' => '12345678Z',
            'code' => 'EMP001',
        ]);
    }

    public function test_update_employee(): void
    {
        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->userId,
            name: 'Juan',
            lastName: 'García',
            email: 'juan@example.com',
        ));

        $employeeId = \DB::table('employees')->where('email', 'juan@example.com')->value('id');

        $this->commandBus->dispatch(new UpdateEmployeeCommand(
            activeUserId: $this->userId,
            employeeId: $employeeId,
            name: 'Juan Carlos',
            lastName: 'García López',
            email: 'juancarlos@example.com',
            isActive: true,
        ));

        $this->assertDatabaseHas('employees', [
            'id' => $employeeId,
            'name' => 'Juan Carlos',
            'last_name' => 'García López',
            'email' => 'juancarlos@example.com',
        ]);
    }

    public function test_delete_employee(): void
    {
        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->userId,
            name: 'Juan',
            lastName: 'García',
            email: 'juan@example.com',
        ));

        $employeeId = \DB::table('employees')->where('email', 'juan@example.com')->value('id');

        $this->commandBus->dispatch(new DeleteEmployeeCommand(
            activeUserId: $this->userId,
            employeeId: $employeeId,
        ));

        $this->assertDatabaseMissing('employees', [
            'id' => $employeeId,
        ]);
    }

    public function test_list_employees(): void
    {
        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->userId,
            name: 'Juan',
            lastName: 'García',
            email: 'juan@example.com',
        ));

        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->userId,
            name: 'María',
            lastName: 'López',
            email: 'maria@example.com',
        ));

        $response = $this->queryBus->ask(new ListEmployeesQuery(
            activeUserId: $this->userId,
            onlyActive: true,
        ));

        $items = $response->toArray();

        $this->assertGreaterThanOrEqual(2, count($items));
    }

    public function test_find_employee(): void
    {
        $this->commandBus->dispatch(new CreateEmployeeCommand(
            activeUserId: $this->userId,
            name: 'Juan',
            lastName: 'García',
            email: 'juan@example.com',
        ));

        $employeeId = \DB::table('employees')->where('email', 'juan@example.com')->value('id');

        $response = $this->queryBus->ask(new FindEmployeeQuery(
            activeUserId: $this->userId,
            employeeId: $employeeId,
        ));

        $data = $response->toArray();

        $this->assertEquals('Juan', $data['name']);
        $this->assertEquals('García', $data['last_name']);
        $this->assertEquals('juan@example.com', $data['email']);
    }
}

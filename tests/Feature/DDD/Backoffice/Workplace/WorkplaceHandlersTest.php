<?php

declare(strict_types=1);

namespace Tests\Feature\DDD\Backoffice\Workplace;

use App\DDD\Backoffice\Workplace\Application\Create\CreateWorkplaceCommand;
use App\DDD\Backoffice\Workplace\Application\Delete\DeleteWorkplaceCommand;
use App\DDD\Backoffice\Workplace\Application\Find\FindWorkplaceQuery;
use App\DDD\Backoffice\Workplace\Application\ListAll\ListWorkplacesQuery;
use App\DDD\Backoffice\Workplace\Application\Update\UpdateWorkplaceCommand;
use App\DDD\Shared\Infrastructure\Bus\CommandBusInterface;
use App\DDD\Shared\Infrastructure\Bus\QueryBusInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class WorkplaceHandlersTest extends TestCase
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

    public function test_create_workplace(): void
    {
        $this->commandBus->dispatch(new CreateWorkplaceCommand(
            activeUserId: $this->userId,
            name: 'Oficina Central',
            address: 'Calle Mayor 1',
            city: 'Madrid',
            postalCode: '28001',
            latitude: 40.4168,
            longitude: -3.7038,
            radius: 200,
        ));

        $this->assertDatabaseHas('workplaces', [
            'name' => 'Oficina Central',
            'address' => 'Calle Mayor 1',
            'city' => 'Madrid',
        ]);
    }

    public function test_update_workplace(): void
    {
        $this->commandBus->dispatch(new CreateWorkplaceCommand(
            activeUserId: $this->userId,
            name: 'Oficina Central',
            address: 'Calle Mayor 1',
            city: 'Madrid',
        ));

        $workplaceId = \DB::table('workplaces')->where('name', 'Oficina Central')->value('id');

        $this->commandBus->dispatch(new UpdateWorkplaceCommand(
            activeUserId: $this->userId,
            workplaceId: $workplaceId,
            name: 'Oficina Principal',
            address: 'Calle Mayor 2',
            city: 'Madrid',
            isActive: true,
        ));

        $this->assertDatabaseHas('workplaces', [
            'id' => $workplaceId,
            'name' => 'Oficina Principal',
            'address' => 'Calle Mayor 2',
        ]);
    }

    public function test_delete_workplace(): void
    {
        $this->commandBus->dispatch(new CreateWorkplaceCommand(
            activeUserId: $this->userId,
            name: 'Oficina Temporal',
        ));

        $workplaceId = \DB::table('workplaces')->where('name', 'Oficina Temporal')->value('id');

        $this->commandBus->dispatch(new DeleteWorkplaceCommand(
            activeUserId: $this->userId,
            workplaceId: $workplaceId,
        ));

        $this->assertDatabaseMissing('workplaces', [
            'id' => $workplaceId,
        ]);
    }

    public function test_list_workplaces(): void
    {
        $this->commandBus->dispatch(new CreateWorkplaceCommand(
            activeUserId: $this->userId,
            name: 'Oficina A',
        ));

        $this->commandBus->dispatch(new CreateWorkplaceCommand(
            activeUserId: $this->userId,
            name: 'Oficina B',
        ));

        $response = $this->queryBus->ask(new ListWorkplacesQuery(
            activeUserId: $this->userId,
            onlyActive: true,
        ));

        $items = $response->toArray();

        $this->assertGreaterThanOrEqual(2, count($items));
    }

    public function test_find_workplace(): void
    {
        $this->commandBus->dispatch(new CreateWorkplaceCommand(
            activeUserId: $this->userId,
            name: 'Oficina Central',
            address: 'Calle Mayor 1',
            city: 'Madrid',
        ));

        $workplaceId = \DB::table('workplaces')->where('name', 'Oficina Central')->value('id');

        $response = $this->queryBus->ask(new FindWorkplaceQuery(
            activeUserId: $this->userId,
            workplaceId: $workplaceId,
        ));

        $data = $response->toArray();

        $this->assertEquals('Oficina Central', $data['name']);
        $this->assertEquals('Calle Mayor 1', $data['address']);
    }
}

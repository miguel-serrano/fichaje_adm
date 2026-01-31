<?php

declare(strict_types=1);

namespace Tests\Feature\DDD\Backoffice\Notification;

use App\DDD\Backoffice\Notification\Application\ListByRecipient\ListNotificationsQuery;
use App\DDD\Backoffice\Notification\Application\MarkAllAsRead\MarkAllAsReadCommand;
use App\DDD\Backoffice\Notification\Application\MarkAsRead\MarkAsReadCommand;
use App\DDD\Backoffice\Notification\Domain\Service\Notifier;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationChannel;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationType;
use App\DDD\Shared\Infrastructure\Bus\CommandBusInterface;
use App\DDD\Shared\Infrastructure\Bus\QueryBusInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NotificationHandlersTest extends TestCase
{
    use RefreshDatabase;

    private CommandBusInterface $commandBus;
    private QueryBusInterface $queryBus;
    private Notifier $notifier;
    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->app->make(CommandBusInterface::class);
        $this->queryBus = $this->app->make(QueryBusInterface::class);
        $this->notifier = $this->app->make(Notifier::class);

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

    public function test_list_notifications(): void
    {
        $this->notifier->notify(
            type: NotificationType::CLOCK_IN_LATE,
            title: 'Test notification',
            body: 'Test body',
            recipientId: $this->userId,
        );

        $this->notifier->notify(
            type: NotificationType::CLOCK_IN_MISSED,
            title: 'Another notification',
            body: 'Another body',
            recipientId: $this->userId,
        );

        $response = $this->queryBus->ask(new ListNotificationsQuery(
            recipientId: $this->userId,
        ));

        $data = $response->toArray();

        $this->assertCount(2, $data['items']);
        $this->assertEquals(2, $data['unread_count']);
    }

    public function test_mark_notification_as_read(): void
    {
        $this->notifier->notify(
            type: NotificationType::CLOCK_IN_LATE,
            title: 'Test notification',
            body: 'Test body',
            recipientId: $this->userId,
        );

        // Get the actual DB id
        $notificationId = \DB::table('notifications')
            ->where('recipient_id', $this->userId)
            ->value('id');

        $this->commandBus->dispatch(new MarkAsReadCommand(
            notificationId: $notificationId,
            recipientId: $this->userId,
        ));

        $response = $this->queryBus->ask(new ListNotificationsQuery(
            recipientId: $this->userId,
        ));

        $data = $response->toArray();

        $this->assertEquals(0, $data['unread_count']);
        $this->assertTrue($data['items'][0]['is_read']);
    }

    public function test_mark_all_notifications_as_read(): void
    {
        $this->notifier->notify(
            type: NotificationType::CLOCK_IN_LATE,
            title: 'Notification 1',
            body: 'Body 1',
            recipientId: $this->userId,
        );

        $this->notifier->notify(
            type: NotificationType::CLOCK_IN_MISSED,
            title: 'Notification 2',
            body: 'Body 2',
            recipientId: $this->userId,
        );

        $this->commandBus->dispatch(new MarkAllAsReadCommand(
            recipientId: $this->userId,
        ));

        $response = $this->queryBus->ask(new ListNotificationsQuery(
            recipientId: $this->userId,
        ));

        $data = $response->toArray();

        $this->assertEquals(0, $data['unread_count']);
    }
}

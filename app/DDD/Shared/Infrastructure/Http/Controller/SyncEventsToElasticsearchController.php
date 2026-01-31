<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Controller;

use App\DDD\Shared\Infrastructure\Event\EventSynchronizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

final class SyncEventsToElasticsearchController extends Controller
{
    public function __construct(
        private readonly EventSynchronizer $eventSynchronizer,
    ) {}

    public function __invoke(): RedirectResponse
    {
        try {
            $result = $this->eventSynchronizer->syncPendingEvents(500);

            $message = sprintf(
                'Sincronización completada: %d eventos sincronizados, %d fallidos.',
                $result['synced'],
                $result['failed'],
            );

            if ($result['failed'] > 0) {
                return redirect()->back()->with('error', $message);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }
}

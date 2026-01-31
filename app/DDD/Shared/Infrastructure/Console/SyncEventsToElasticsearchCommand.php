<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Console;

use App\DDD\Shared\Infrastructure\Event\EventSynchronizer;
use Illuminate\Console\Command;

final class SyncEventsToElasticsearchCommand extends Command
{
    protected $signature = 'events:sync 
                            {--batch=100 : Número de eventos por lote}
                            {--continuous : Ejecutar continuamente hasta que no haya pendientes}';

    protected $description = 'Sincroniza eventos pendientes de MySQL a Elasticsearch';

    public function handle(EventSynchronizer $synchronizer): int
    {
        $batchSize = (int) $this->option('batch');
        $continuous = $this->option('continuous');

        $this->info('Iniciando sincronización de eventos...');

        $totalSynced = 0;
        $totalFailed = 0;

        do {
            $result = $synchronizer->syncPendingEvents($batchSize);

            $totalSynced += $result['synced'];
            $totalFailed += $result['failed'];

            if ($result['synced'] > 0 || $result['failed'] > 0) {
                $this->line(sprintf(
                    '  → Sincronizados: %d, Fallidos: %d',
                    $result['synced'],
                    $result['failed']
                ));
            }

            foreach ($result['errors'] as $error) {
                $this->warn("  Error en {$error['event_id']}: {$error['error']}");
            }

            $hasMore = $result['synced'] === $batchSize;

        } while ($continuous && $hasMore);

        $this->newLine();
        $this->info("✓ Sincronización completada");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total sincronizados', $totalSynced],
                ['Total fallidos', $totalFailed],
            ]
        );

        // Mostrar estadísticas
        $stats = $synchronizer->getStats();
        $this->newLine();
        $this->info("Estadísticas:");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total eventos', $stats['total_events']],
                ['Pendientes', $stats['pending_events']],
            ]
        );

        return $totalFailed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}

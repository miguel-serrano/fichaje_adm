<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Console;

use App\DDD\Shared\Infrastructure\Event\EventSynchronizer;
use DateTimeImmutable;
use Illuminate\Console\Command;

final class ResyncEventsCommand extends Command
{
    protected $signature = 'events:resync 
                            {--from= : Fecha desde (Y-m-d)}
                            {--to= : Fecha hasta (Y-m-d)}
                            {--event= : Filtrar por tipo de evento}
                            {--ids=* : IDs de eventos específicos}';

    protected $description = 'Re-encola eventos para volver a sincronizarlos';

    public function handle(EventSynchronizer $synchronizer): int
    {
        $ids = $this->option('ids');

        if (!empty($ids)) {
            return $this->resyncByIds($synchronizer, $ids);
        }

        return $this->resyncByDateRange($synchronizer);
    }

    private function resyncByIds(EventSynchronizer $synchronizer, array $ids): int
    {
        $this->info('Re-encolando eventos por IDs...');

        $result = $synchronizer->resyncEvents($ids);

        $this->info("✓ {$result['requeued']} eventos re-encolados");

        return Command::SUCCESS;
    }

    private function resyncByDateRange(EventSynchronizer $synchronizer): int
    {
        $from = $this->option('from');
        $to = $this->option('to');

        if (!$from || !$to) {
            $this->error('Debes especificar --from y --to, o --ids');
            return Command::FAILURE;
        }

        $this->info("Re-encolando eventos desde {$from} hasta {$to}...");

        $result = $synchronizer->resyncByDateRange(
            from: new DateTimeImmutable($from),
            to: new DateTimeImmutable($to . ' 23:59:59'),
            eventName: $this->option('event'),
        );

        $this->info("✓ {$result['requeued']} eventos re-encolados");

        return Command::SUCCESS;
    }
}

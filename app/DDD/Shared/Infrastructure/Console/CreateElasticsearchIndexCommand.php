<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Console;

use App\DDD\Shared\Infrastructure\Event\ElasticsearchEventStore;
use Illuminate\Console\Command;

final class CreateElasticsearchIndexCommand extends Command
{
    protected $signature = 'elasticsearch:create-index 
                            {--force : Eliminar índice existente antes de crear}';

    protected $description = 'Crea el índice de eventos en Elasticsearch';

    public function handle(ElasticsearchEventStore $eventStore): int
    {
        if ($this->option('force')) {
            $this->warn('Eliminando índice existente...');
            $eventStore->deleteIndex();
        }

        $this->info('Creando índice de eventos en Elasticsearch...');

        try {
            $eventStore->createIndex();
            $this->info('✓ Índice creado correctamente');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}

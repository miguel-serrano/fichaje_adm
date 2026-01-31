<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Laravel;

use App\DDD\Shared\Application\Event\SearchEventsQuery;
use App\DDD\Shared\Application\Event\SearchEventsQueryHandler;
use App\DDD\Shared\Domain\Event\EventBusInterface;
use App\DDD\Shared\Domain\Event\EventStoreInterface;
use App\DDD\Shared\Infrastructure\Bus\CommandBusInterface;
use App\DDD\Shared\Infrastructure\Bus\LoggingCommandBus;
use App\DDD\Shared\Infrastructure\Bus\QueryBusInterface;
use App\DDD\Shared\Infrastructure\Bus\SimpleCommandBus;
use App\DDD\Shared\Infrastructure\Bus\SimpleQueryBus;
use App\DDD\Shared\Infrastructure\Bus\TransactionalCommandBus;
use App\DDD\Shared\Infrastructure\Console\CreateElasticsearchIndexCommand;
use App\DDD\Shared\Infrastructure\Console\ResyncEventsCommand;
use App\DDD\Shared\Infrastructure\Console\SyncEventsToElasticsearchCommand;
use App\DDD\Shared\Infrastructure\Event\CorrelationIdProvider;
use App\DDD\Shared\Infrastructure\Event\ElasticsearchEventStore;
use App\DDD\Shared\Infrastructure\Event\EventEnricher;
use App\DDD\Shared\Infrastructure\Event\EventSynchronizer;
use App\DDD\Shared\Infrastructure\Event\GeoIpProvider;
use App\DDD\Shared\Infrastructure\Event\LaravelEventBus;
use App\DDD\Shared\Infrastructure\Event\MysqlEventStore;
use App\DDD\Shared\Infrastructure\Event\OutboxEventBus;
use App\DDD\Shared\Infrastructure\Event\RequestContextProvider;
use App\DDD\Shared\Infrastructure\Event\UserContextProvider;
use Illuminate\Support\ServiceProvider;

final class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Command Bus (con transacciones y logging)
        $this->registerCommandBus();

        // Query Bus (singleton)
        $this->app->singleton(QueryBusInterface::class, SimpleQueryBus::class);

        // Correlation ID Provider
        $this->app->singleton(CorrelationIdProvider::class);

        // Event Enricher
        $this->registerEventEnricher();

        // MySQL Event Store (siempre disponible)
        $this->registerMysqlEventStore();

        // Elasticsearch Event Store (opcional)
        $this->registerElasticsearchEventStore();

        // Event Synchronizer
        $this->registerEventSynchronizer();

        // Event Bus (usa Outbox pattern)
        $this->registerEventBus();

        // Query handlers
        $this->registerQueryHandlers();
    }

    public function boot(): void
    {
        // Registrar comandos de consola
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateElasticsearchIndexCommand::class,
                SyncEventsToElasticsearchCommand::class,
                ResyncEventsCommand::class,
            ]);
        }
    }

    private function registerEventEnricher(): void
    {
        // Providers de contexto
        $this->app->singleton(UserContextProvider::class);
        $this->app->singleton(RequestContextProvider::class);

        // GeoIP Provider (opcional)
        $this->app->singleton(GeoIpProvider::class, function () {
            return new GeoIpProvider(
                provider: config('services.geoip.provider', 'ip-api'),
                maxMindLicenseKey: config('services.maxmind.license_key'),
            );
        });

        // Event Enricher principal
        $this->app->singleton(EventEnricher::class, function ($app) {
            return new EventEnricher(
                correlationIdProvider: $app->make(CorrelationIdProvider::class),
                userContextProvider: $app->make(UserContextProvider::class),
                requestContextProvider: $app->make(RequestContextProvider::class),
                geoIpProvider: config('services.geoip.enabled', false)
                    ? $app->make(GeoIpProvider::class)
                    : null,
            );
        });
    }

    private function registerMysqlEventStore(): void
    {
        $this->app->singleton(MysqlEventStore::class, function ($app) {
            return new MysqlEventStore(
                enricher: $app->make(EventEnricher::class),
            );
        });
    }

    private function registerCommandBus(): void
    {
        $this->app->singleton(CommandBusInterface::class, function ($app) {
            $simpleCommandBus = new SimpleCommandBus($app);

            // Envolver con logging
            $loggingBus = new LoggingCommandBus($simpleCommandBus);

            // Envolver con transacciones
            return new TransactionalCommandBus($loggingBus);
        });
    }

    private function registerElasticsearchEventStore(): void
    {
        $this->app->singleton(ElasticsearchEventStore::class, function ($app) {
            return new ElasticsearchEventStore(
                host: config('elasticsearch.host', 'http://localhost:9200'),
                username: config('elasticsearch.username'),
                password: config('elasticsearch.password'),
                apiKey: config('elasticsearch.api_key'),
            );
        });

        // Bind interface solo si está habilitado
        if (config('elasticsearch.enabled', false)) {
            $this->app->bind(EventStoreInterface::class, ElasticsearchEventStore::class);
        }
    }

    private function registerEventSynchronizer(): void
    {
        $this->app->singleton(EventSynchronizer::class, function ($app) {
            return new EventSynchronizer(
                mysqlEventStore: $app->make(MysqlEventStore::class),
                elasticsearchEventStore: $app->make(ElasticsearchEventStore::class),
            );
        });
    }

    private function registerEventBus(): void
    {
        $this->app->singleton(EventBusInterface::class, function ($app) {
            // Siempre usar OutboxEventBus que persiste en MySQL
            return new OutboxEventBus(
                container: $app,
                mysqlEventStore: $app->make(MysqlEventStore::class),
            );
        });
    }

    private function registerQueryHandlers(): void
    {
        /** @var SimpleQueryBus $queryBus */
        $queryBus = $this->app->make(QueryBusInterface::class);

        // Solo registrar búsqueda en ES si está habilitado
        if (config('elasticsearch.enabled', false)) {
            $queryBus->map([
                SearchEventsQuery::class => SearchEventsQueryHandler::class,
            ]);
        }
    }
}

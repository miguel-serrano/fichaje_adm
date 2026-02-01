<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use App\DDD\Shared\Domain\Event\DomainEvent;
use App\DDD\Shared\Domain\Event\EventStoreInterface;
use App\DDD\Shared\Domain\Event\StoredEvent;
use DateTimeImmutable;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

final class ElasticsearchEventStore implements EventStoreInterface
{
    private const INDEX_NAME = 'domain_events';

    private ?Client $client = null;

    public function __construct(
        private readonly string $host,
        private readonly ?string $username = null,
        private readonly ?string $password = null,
        private readonly ?string $apiKey = null,
    ) {}

    public function append(DomainEvent ...$events): void
    {
        if (empty($events)) {
            return;
        }

        $params = ['body' => []];

        foreach ($events as $event) {
            $storedEvent = StoredEvent::fromDomainEvent($event, $this->buildMetadata());

            $params['body'][] = [
                'index' => [
                    '_index' => self::INDEX_NAME,
                    '_id' => $storedEvent->eventId,
                ],
            ];

            $params['body'][] = $storedEvent->toArray();
        }

        $this->getClient()->bulk($params);
    }

    /**
     * Añade un StoredEvent (desde MySQL) a Elasticsearch
     */
    public function appendStoredEvent(StoredEvent $storedEvent): void
    {
        $this->getClient()->index([
            'index' => self::INDEX_NAME,
            'id' => $storedEvent->eventId,
            'body' => $storedEvent->toArray(),
        ]);
    }

    public function getByAggregateId(string $aggregateId): array
    {
        $response = $this->getClient()->search([
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'term' => [
                        'aggregate_id.keyword' => $aggregateId,
                    ],
                ],
                'sort' => [
                    ['occurred_on' => 'asc'],
                ],
                'size' => 10000,
            ],
        ]);

        return $this->mapHitsToStoredEvents($response);
    }

    public function getByEventName(string $eventName, int $limit = 100): array
    {
        $response = $this->getClient()->search([
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'term' => [
                        'event_name.keyword' => $eventName,
                    ],
                ],
                'sort' => [
                    ['occurred_on' => 'desc'],
                ],
                'size' => $limit,
            ],
        ]);

        return $this->mapHitsToStoredEvents($response);
    }

    public function getByDateRange(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?string $eventName = null,
        int $limit = 1000,
    ): array {
        $query = [
            'bool' => [
                'must' => [
                    [
                        'range' => [
                            'occurred_on' => [
                                'gte' => $from->format('Y-m-d\TH:i:s.uP'),
                                'lte' => $to->format('Y-m-d\TH:i:s.uP'),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if ($eventName !== null) {
            $query['bool']['must'][] = [
                'term' => [
                    'event_name.keyword' => $eventName,
                ],
            ];
        }

        $response = $this->getClient()->search([
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => $query,
                'sort' => [
                    ['occurred_on' => 'asc'],
                ],
                'size' => $limit,
            ],
        ]);

        return $this->mapHitsToStoredEvents($response);
    }

    public function search(array $filters, int $limit = 100, int $offset = 0): array
    {
        $must = [];

        if (isset($filters['event_name'])) {
            $must[] = ['term' => ['event_name.keyword' => $filters['event_name']]];
        }

        if (isset($filters['aggregate_id'])) {
            $must[] = ['term' => ['aggregate_id.keyword' => $filters['aggregate_id']]];
        }

        if (isset($filters['from']) || isset($filters['to'])) {
            $range = [];
            if (isset($filters['from'])) {
                $range['gte'] = $filters['from'];
            }
            if (isset($filters['to'])) {
                $range['lte'] = $filters['to'];
            }
            $must[] = ['range' => ['occurred_on' => $range]];
        }

        if (isset($filters['payload_search'])) {
            $must[] = ['match' => ['payload' => $filters['payload_search']]];
        }

        if (isset($filters['payload_field']) && isset($filters['payload_value'])) {
            $must[] = ['term' => ["payload.{$filters['payload_field']}" => $filters['payload_value']]];
        }

        $query = empty($must) ? ['match_all' => (object) []] : ['bool' => ['must' => $must]];

        $response = $this->getClient()->search([
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => $query,
                'sort' => [
                    ['occurred_on' => 'desc'],
                ],
                'size' => $limit,
                'from' => $offset,
            ],
        ]);

        return $this->mapHitsToStoredEvents($response);
    }

    /**
     * Crea el índice con el mapping apropiado
     */
    public function createIndex(): void
    {
        $exists = $this->getClient()->indices()->exists(['index' => self::INDEX_NAME]);

        if ($exists->asBool()) {
            return;
        }

        $this->getClient()->indices()->create([
            'index' => self::INDEX_NAME,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                ],
                'mappings' => [
                    'properties' => [
                        'event_id' => ['type' => 'keyword'],
                        'event_name' => ['type' => 'keyword'],
                        'aggregate_id' => ['type' => 'keyword'],
                        'payload' => [
                            'type' => 'object',
                            'enabled' => true,
                        ],
                        'occurred_on' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd'T'HH:mm:ss.SSSSSSZ||yyyy-MM-dd'T'HH:mm:ssZ",
                        ],
                        'metadata' => [
                            'type' => 'object',
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Elimina el índice (usar con precaución)
     */
    public function deleteIndex(): void
    {
        $exists = $this->getClient()->indices()->exists(['index' => self::INDEX_NAME]);

        if ($exists->asBool()) {
            $this->getClient()->indices()->delete(['index' => self::INDEX_NAME]);
        }
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $builder = ClientBuilder::create()
                ->setHosts([$this->host]);

            if ($this->apiKey !== null) {
                $builder->setApiKey($this->apiKey);
            } elseif ($this->username !== null && $this->password !== null) {
                $builder->setBasicAuthentication($this->username, $this->password);
            }

            $this->client = $builder->build();
        }

        return $this->client;
    }

    private function buildMetadata(): array
    {
        return [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'timestamp' => (new DateTimeImmutable())->format('Y-m-d\TH:i:s.uP'),
            'environment' => app()->environment(),
        ];
    }

    /**
     * @return StoredEvent[]
     */
    private function mapHitsToStoredEvents(mixed $response): array
    {
        $hits = $response['hits']['hits'] ?? [];

        return array_map(
            fn (array $hit) => StoredEvent::fromArray($hit['_source']),
            $hits,
        );
    }
}

<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Event Store Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para el almacenamiento de eventos de dominio en Elasticsearch
    |
    */

    'enabled' => env('ELASTICSEARCH_ENABLED', false),

    'host' => env('ELASTICSEARCH_HOST', 'http://localhost:9200'),

    'username' => env('ELASTICSEARCH_USERNAME'),

    'password' => env('ELASTICSEARCH_PASSWORD'),

    'api_key' => env('ELASTICSEARCH_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Index Settings
    |--------------------------------------------------------------------------
    */

    'index' => [
        'name' => env('ELASTICSEARCH_INDEX', 'domain_events'),
        'shards' => env('ELASTICSEARCH_SHARDS', 1),
        'replicas' => env('ELASTICSEARCH_REPLICAS', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    */

    'retries' => env('ELASTICSEARCH_RETRIES', 3),
];

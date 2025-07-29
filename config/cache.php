<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Armazenamento de Cache Padrão
    |--------------------------------------------------------------------------
    |
    | Esta opção controla o armazenamento de cache padrão que será usado
    | pelo framework. Esta conexão é utilizada se outra não for explicitamente
    | especificada ao executar uma operação de cache dentro da aplicação.
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Store Pruning
    |--------------------------------------------------------------------------
    |
    | Here you may configure the automatic pruning of cache stores. This
    | feature allows you to automatically remove old cache entries when
    | they are no longer needed.
    |
    */

    'pruning' => [
        'default' => [
            'driver' => 'file',
            'hours' => 24,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Armazenamentos de Cache
    |--------------------------------------------------------------------------
    |
    | Aqui você pode definir todos os "armazenamentos" de cache para sua
    | aplicação, bem como seus drivers. Você pode até definir múltiplos
    | armazenamentos para o mesmo driver de cache para agrupar tipos de
    | itens armazenados em seus caches.
    |
    | Drivers suportados: "array", "database", "file", "memcached",
    |                    "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'),
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Prefixo da Chave de Cache
    |--------------------------------------------------------------------------
    |
    | Ao utilizar os armazenamentos de cache APC, database, memcached, Redis
    | e DynamoDB, pode haver outras aplicações usando o mesmo cache. Por
    | essa razão, você pode prefixar cada chave de cache para evitar colisões.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),

];

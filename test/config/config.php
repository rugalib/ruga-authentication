<?php


return [
    'db' => [
        'driver' => 'Pdo_Mysql',
        'host' => '127.0.0.1',
        'database' => '',
        'username' => '',
        'password' => '',
        'driver_options' => [
        ],
        \Ruga\Db\Schema\Updater::class => [
            \Ruga\Db\Schema\Updater::CONF_REQUESTED_VERSION => 1,
            \Ruga\Db\Schema\Updater::CONF_SCHEMA_DIRECTORY => __DIR__ . '/ruga-dbschema',
        ],
    ],
    'cache' => [
        'adapter' => [
            'name' => \Laminas\Cache\Storage\Adapter\Memory::class,
            'options' => [
                'ttl' => PHP_INT_MAX,
            ],
        ],
        'plugins' => [
            'exception_handler' => [
                'throw_exceptions' => false,
            ],
            'serializer' => [],
        ],
    ],
];


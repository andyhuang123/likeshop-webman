<?php

return [
    'default' => getenv('DB_CONNECTION','mysql'),
    'connections' => [
        'mysql' => [
            'type'            => 'mysql',
            'hostname'        => getenv('DB_HOST'),
            'database'        => getenv('DB_DATABASE'),
            'username'        => getenv('DB_USERNAME'),
            'password'        => getenv('DB_PASSWORD'),
            'hostport'        => getenv('DB_PORT'),
            'charset'         => 'utf8',
            'prefix'          => getenv('DB_PREFIX'),
            'break_reconnect' => true,
            'trigger_sql'     => getenv('SQL_DEBUG',false),
            'bootstrap'       => '',
            'auto_timestamp'  => true,
            'datetime_format' => false,
        ],
        'pgsql' => [
            'type'            => 'pgsql',
            'hostname'        => getenv('DB_HOST'),
            'database'        => getenv('DB_DATABASE'),
            'username'        => getenv('DB_USERNAME'),
            'password'        => getenv('DB_PASSWORD'),
            'hostport'        => getenv('DB_PORT'),
            'charset'         => 'utf8',
            'prefix'          => getenv('DB_PREFIX'),
            'break_reconnect' => true,
            'trigger_sql'     => getenv('SQL_DEBUG',false),
            'bootstrap'       => '',
            'auto_timestamp'  => true,
            'datetime_format' => false,
        ]
    ],
];

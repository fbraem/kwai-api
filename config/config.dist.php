<?php
/**
 * Kwai API configuration for testapi
 */

use Monolog\Logger;

error_reporting(E_ALL);
ini_set("display_errors", 1);

return [
    'database' => [
        'production' => [
            'dsn' => '',
            'host' => '',
            'adapter' => 'mysql',
            'name' => '',
            'user' => '',
            'pass' => '',
            'charset' => 'utf8',
            'prefix' => ''
        ]
    ],
    'default_database' => 'production',
    'logger' => [
        'kwai' => [
            'file' => '',
            'level' => Logger::DEBUG
        ],
        'database' => [
            'file' => '',
            'level' => Logger::DEBUG
        ]
    ],
    'files' => [
        'local' => '',
        'url' => ''
    ],
    'security' => [
        'secret' => '',
        'algorithm' => 'HS256',
        'relaxed' => [ 'localhost', '' ]
    ],
    'mail' => [
        'host' => '',
        'user' => '',
        'pass' => '',
        'port' => 587,
        'from' => [ ],
        'subject' => ''
    ],
    'website' => [
        'url' => '',
        'email' => ''
    ],
    'cors' => [
        'server' => [
            'scheme' => 'https',
            'host' => ''
        ],
        'origin' => [
            ''
        ]
    ]
];

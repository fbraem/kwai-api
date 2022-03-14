<?php
/**
 * Phinx migration configuration
 */
require __DIR__ . '/../autoload.php';

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;

/** @var Connection $db */
$db = depends('kwai.database', DatabaseDependency::class);

$environments = [
    'default_environment' => 'kwai',
    'kwai' => [
        'name' => $db->getDatabaseName(),
        'connection' => $db->getPdo()
    ]
];

return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/kwai/Modules/Applications/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Users/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Mails/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/News/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Pages/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Trainings/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Coaches/Infrastructure/Migrations'
        ]
    ],
    'environments' => $environments
];

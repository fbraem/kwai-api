<?php
/**
 * Phinx migration configuration
 */
require __DIR__ . '/../autoload.php';
use Kwai\Core\Infrastructure\Dependencies\Settings;

$config = (new Settings())->create();

$environments = $config['database'];
$environments['default_environment'] = $config['default_database'];

return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/migrations',
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

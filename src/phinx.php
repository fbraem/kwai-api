<?php
/**
 * Phinx migration configuration
 */

use Kwai\Core\Infrastructure\Dependencies\Settings;

$config = (new Settings())();

$environments = $config['database'];
$environments['default_database'] = $config['default_database'];

return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/kwai/Modules/Applications/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Users/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Mails/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/News/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Pages/Infrastructure/Migrations',
            __DIR__ . '/domain/Content/migrations',
            __DIR__ . '/domain/Person/migrations',
            __DIR__ . '/domain/Game/migrations',
            __DIR__ . '/domain/Team/migrations',
            __DIR__ . '/domain/Club/migrations',
            __DIR__ . '/domain/Event/migrations',
            __DIR__ . '/sport/judo/domain/Member/migrations',
            __DIR__ . '/domain/Training/migrations',
            __DIR__ . '/domain/Member/migrations',
        ]
    ],
    'environments' => $environments
];

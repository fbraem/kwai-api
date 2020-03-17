<?php
/**
 * Phinx migration configuration
 */

use Core\Clubman;

$application = Clubman::getApplication();
$config = $application->getContainer()->get('settings');

$environments = $config['database'];
$environments['default_database'] = $config['default_database'];

return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/kwai/Modules/Users/Infrastructure/Migrations',
            __DIR__ . '/kwai/Modules/Mails/Infrastructure/Migrations',
            __DIR__ . '/domain/Category/migrations',
            __DIR__ . '/domain/News/migrations',
            __DIR__ . '/domain/Content/migrations',
            __DIR__ . '/domain/Page/migrations',
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

<?php
/**
 * Phinx migration configuration
 */
$application = \Core\Clubman::getApplication();
$config = $application->getConfig();

$environments = $config->database->toArray();
$environments['default_database'] = $config->default_database;

return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/domain/User/migrations',
            __DIR__ . '/domain/Category/migrations',
            __DIR__ . '/domain/News/migrations',
            __DIR__ . '/domain/Auth/migrations',
            __DIR__ . '/domain/Content/migrations',
            __DIR__ . '/domain/Page/migrations',
            __DIR__ . '/domain/Person/migrations',
            __DIR__ . '/domain/Sport/Judo/Member/migrations'
        ]
    ],
    'environments' => $environments
];

<?php
/**
 * Phoenix migration configuration
 */
$application = \Core\Clubman::getApplication();
$config = $application->getConfig();

return [
    'migration_dirs' => [
        'users' => __DIR__ . '/domain/User/migrations'
    ],
    'environments' => $config->database->toArray(),
    'default_environment' => $config->default_environment
];

<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @return Connection
 * @throws DatabaseException
 */
function withDatabase()
{
    $config = (new Settings())();
    // TODO: see if we can use DatabaseDependency here ...
    $logger = new Logger('kwai-db');
    if (isset($config['logger'])) {
        if (isset($config['logger']['file'])) {
            $logger->pushHandler(
                new StreamHandler(
                    $config['logger']['file'],
                    $config['logger']['level'] ?? Logger::DEBUG
                )
            );
        }
    }

    return new Connection(
        $config['database']['test']['dsn'],
        $config['database']['test']['user'],
        $config['database']['test']['pass'],
        $logger
    );
}

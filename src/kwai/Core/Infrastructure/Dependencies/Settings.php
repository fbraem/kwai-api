<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

/**
 * Class Settings
 */
class Settings
{
    public function __invoke()
    {
        $config = include __DIR__ . '/../../../../../api/config.php';
        $config['displayErrorDetails'] = true;
        $config['determineRouteBeforeAppMiddleware'] = true;
        $config['outputBuffering'] = 'append';
        $config['httpVersion'] = '1.1';
        $config['responseChunkSize'] = 4096;
        return $config;
    }
}

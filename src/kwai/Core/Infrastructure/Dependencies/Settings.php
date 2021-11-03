<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

/**
 * Class Settings
 */
class Settings implements Dependency
{
    public function create()
    {
        $config = include __DIR__ . '/../../../../../config.php';
        $config['displayErrorDetails'] = true;
        $config['determineRouteBeforeAppMiddleware'] = true;
        $config['outputBuffering'] = 'append';
        $config['httpVersion'] = '1.1';
        $config['responseChunkSize'] = 4096;
        return $config;
    }
}

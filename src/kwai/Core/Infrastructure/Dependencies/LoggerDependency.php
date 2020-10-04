<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class LoggerDependency
 *
 * A logger dependency: monolog will be used as logger.
 */
class LoggerDependency implements Dependency
{
    public function __invoke(array $settings)
    {
        $logger = new Logger('kwai');

        if (isset($settings['logger']['kwai'])) {
            if (isset($settings['logger']['kwai']['file'])) {
                $logger->pushHandler(
                    new StreamHandler(
                        $settings['logger']['kwai']['file'],
                        $settings['logger']['kwai']['level'] ?? Logger::DEBUG
                    )
                );
            }
        }

        return $logger;
    }
}

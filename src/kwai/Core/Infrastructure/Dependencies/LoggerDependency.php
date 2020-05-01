<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

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
        return new Logger('kwai');
    }
}

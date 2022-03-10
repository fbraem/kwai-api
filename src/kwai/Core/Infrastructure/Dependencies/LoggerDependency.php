<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Configuration\Configuration;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use function depends;

/**
 * Class LoggerDependency
 *
 * A logger dependency: monolog will be used as logger.
 */
class LoggerDependency implements Dependency
{
    public function __construct(
        private ?Configuration $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    public function create()
    {
        return $this->settings
            ->getLoggerConfiguration()
            ->createLogger('kwai')
        ;
    }
}

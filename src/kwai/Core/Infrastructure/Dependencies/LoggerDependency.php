<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

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
        private ?array $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    public function create()
    {
        $logger = new Logger('kwai');

        if (isset($this->settings['logger']['kwai'])) {
            if (isset($this->settings['logger']['kwai']['file'])) {
                $logger->pushHandler(
                    new StreamHandler(
                        $this->settings['logger']['kwai']['file'],
                        $this->settings['logger']['kwai']['level'] ?? Logger::DEBUG
                    )
                );
            }
        }

        return $logger;
    }
}

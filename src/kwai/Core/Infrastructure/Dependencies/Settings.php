<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Configuration\Configuration;

/**
 * Class Settings
 */
class Settings implements Dependency
{
    public function create(): Configuration
    {
        return Configuration::createFromFile(
            __DIR__ . '/../../../../../config',
            '.kwai'
        );
    }
}

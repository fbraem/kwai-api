<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;

/**
 * Interface Configurable
 */
interface Configurable
{
    public static function createFromVariables(array $variables): self;

    public static function validate(Dotenv $env): void;
}

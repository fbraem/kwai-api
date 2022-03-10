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
    public function load(array $variables): void;

    public function validate(Dotenv $env): void;
}

<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);


namespace Kwai\Core\Infrastructure\Dependencies;

/**
 * Interface Dependency
 */
interface Dependency
{
    public function create(): mixed;
}

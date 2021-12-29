<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

/**
 * Class Table
 */
class Table
{
    public function __construct(
        private string $value
    ) {
    }

    use TableTrait;
}

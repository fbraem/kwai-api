<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use Attribute;

/**
 * Class TableAttribute
 *
 * An attribute that defines the table name of a database schema.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class TableAttribute
{
    public function __construct(
        public string $name
    ) {
    }
}

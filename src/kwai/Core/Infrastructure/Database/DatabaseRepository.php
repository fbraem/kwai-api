<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

/**
 * Class DatabaseRepository
 *
 * Base class for database repositories
 */
class DatabaseRepository
{
    protected Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
}

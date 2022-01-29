<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain\Exceptions;

use Exception;

/**
 * Class RoleNotFoundException
 *
 * Raised when a role couldn't be found.
 */
class RoleNotFoundException extends Exception
{
    private int $id;

    /**
     * RoleNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Role not found');
        $this->id = $id;
    }

    public function __toString(): string
    {
        return __CLASS__ . ': Role(' . $this->id . ') not found';
    }
}

<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Domain\Exceptions;

use Exception;

/**
 * Class MemberNotFoundException
 *
 * Raised when a member can't be found.
 */
class MemberNotFoundException extends Exception
{
    private int $id;

    /**
     * MemberNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Member not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Member(' . $this->id . ') not found';
    }
}

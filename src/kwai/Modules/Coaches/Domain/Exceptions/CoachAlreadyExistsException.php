<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Domain\Exceptions;

use Exception;

/**
 * Class CoachAlreadyExistsException
 *
 * Raised when there is already a coach created with the given member
 */
class CoachAlreadyExistsException extends Exception
{
    private int $id;

    /**
     * CoachNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Coach already exists');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Coach with member(' . $this->id . ') already exists';
    }
}

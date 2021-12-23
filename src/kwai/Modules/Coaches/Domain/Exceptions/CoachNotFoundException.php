<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Domain\Exceptions;

use Exception;

/**
 * Class CoachNotFoundException
 *
 * Raised when a coach couldn't be found.
 */
class CoachNotFoundException extends Exception
{
    private int $id;

    /**
     * CoachNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Coach not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Coach(' . $this->id . ') not found';
    }
}

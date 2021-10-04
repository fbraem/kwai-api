<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Domain\Exceptions;

use Exception;

/**
 * Class TeamNotFoundException
 *
 * Raised when a team can't be found.
 */
class TeamNotFoundException extends Exception
{
    private int $id;

    /**
     * TeamNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Team not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Team(' . $this->id . ') not found';
    }
}

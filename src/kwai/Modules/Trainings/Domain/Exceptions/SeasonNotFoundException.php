<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\Exceptions;

use Exception;

/**
 * Class SeasonNotFoundException
 *
 * Thrown when a season can't be found
 */
class SeasonNotFoundException extends Exception
{
    private int $id;

    /**
     * SeasonNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Season not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Season(' . $this->id . ') not found';
    }
}

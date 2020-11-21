<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\Exceptions;

use Exception;

/**
 * Class TrainingNotFoundException
 *
 * Thrown when a training can't be found
 */
class TrainingNotFoundException extends Exception
{
    private int $id;

    /**
     * TrainingNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Training not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Training(' . $this->id . ') not found';
    }
}

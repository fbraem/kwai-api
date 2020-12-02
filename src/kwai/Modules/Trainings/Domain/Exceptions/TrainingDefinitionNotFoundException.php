<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\Exceptions;

use Exception;

/**
 * Class TrainingDefinitionNotFoundException
 *
 * Raised when a definition is not found.
 */
class TrainingDefinitionNotFoundException extends Exception
{
    private int $id;

    /**
     * TrainingDefinitionNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Training Definition not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Training Definition(' . $this->id . ') not found';
    }
}

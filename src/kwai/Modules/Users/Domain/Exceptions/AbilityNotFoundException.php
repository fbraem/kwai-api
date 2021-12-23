<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain\Exceptions;

use Exception;

/**
 * Class AbilityNotFoundException
 *
 * Raised when an ability couldn't be found.
 */
class AbilityNotFoundException extends Exception
{
    private int $id;

    /**
     * AbilityNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Ability not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Ability(' . $this->id . ') not found';
    }
}

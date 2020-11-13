<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Domain\Exceptions;

/**
 * Class ApplicationNotFoundException
 */
class ApplicationNotFoundException extends \Exception
{
    private int $id;

    /**
     * ApplicationNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Application not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Application(' . $this->id . ') not found';
    }
}

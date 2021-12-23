<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain\Exceptions;

use Exception;

/**
 * Class ApplicationNotFoundException
 */
class ApplicationNotFoundException extends Exception
{
    /**
     * ApplicationNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(private int $id)
    {
        parent::__construct('Application not found');
    }

    public function __toString()
    {
        return __CLASS__ . ': Application(' . $this->id . ') not found';
    }
}

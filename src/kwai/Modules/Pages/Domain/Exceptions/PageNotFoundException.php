<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Domain\Exceptions;

use Exception;

/**
 * Class PageNotFoundException
 */
class PageNotFoundException extends Exception
{
    private int $id;

    /**
     * PageNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Page not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Page(' . $this->id . ') not found';
    }
}

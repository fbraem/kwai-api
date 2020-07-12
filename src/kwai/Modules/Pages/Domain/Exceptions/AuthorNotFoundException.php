<?php
/**
 * @package Pages
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Domain\Exceptions;

use Exception;

/**
 * Class AuthorNotFoundException
 */
class AuthorNotFoundException extends Exception
{
    private int $id;

    /**
     * AuthorNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Author not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Author(' . $this->id . ') not found';
    }
}

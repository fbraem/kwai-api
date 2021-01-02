<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain\Exceptions;

use Exception;

/**
 * Class AuthorNotFoundException
 */
class AuthorNotFoundException extends Exception
{
    /**
     * AuthorNotFoundException constructor.
     *
     * @param int|string $id
     */
    public function __construct(private int $id)
    {
        parent::__construct('Author not found');
    }

    public function __toString()
    {
        return __CLASS__ . ': Author(' . $this->id . ') not found';
    }
}

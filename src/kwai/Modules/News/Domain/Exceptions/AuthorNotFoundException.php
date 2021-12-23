<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain\Exceptions;

use Exception;
use Kwai\Core\Domain\ValueObjects\UniqueId;

/**
 * Class AuthorNotFoundException
 */
class AuthorNotFoundException extends Exception
{
    /**
     * AuthorNotFoundException constructor.
     *
     * @param int|UniqueId $id
     */
    public function __construct(private int|UniqueId $id)
    {
        parent::__construct('Author not found');
    }

    public function __toString()
    {
        return __CLASS__ . ': Author(' . strval($this->id) . ') not found';
    }
}

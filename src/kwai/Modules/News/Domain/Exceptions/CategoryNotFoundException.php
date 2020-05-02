<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain\Exceptions;

use Exception;

/**
 * Class CategoryNotFoundException
 */
class CategoryNotFoundException extends Exception
{
    private int $id;

    /**
     * CategoryNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Category not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Category(' . $this->id . ') not found';
    }
}

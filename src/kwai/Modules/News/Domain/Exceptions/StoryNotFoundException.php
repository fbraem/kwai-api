<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain\Exceptions;

use Exception;

/**
 * Class StoryNotFoundException
 */
class StoryNotFoundException extends Exception
{
    /**
     * StoryNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(private int $id)
    {
        parent::__construct('Story not found');
    }

    public function __toString()
    {
        return __CLASS__ . ': Story(' . $this->id . ') not found';
    }
}

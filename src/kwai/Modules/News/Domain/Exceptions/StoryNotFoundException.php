<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain\Exceptions;

use Exception;

/**
 * Class StoryNotFoundException
 */
class StoryNotFoundException extends Exception
{
    private int $id;

    /**
     * StoryNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Story not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Story(' . $this->id . ') not found';
    }
}

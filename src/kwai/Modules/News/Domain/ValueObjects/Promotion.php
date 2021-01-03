<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain\ValueObjects;

use Kwai\Core\Domain\ValueObjects\Timestamp;

/**
 * Class Promotion
 *
 * Information about the promotion of the story.
 * A promoted story can be viewed on top of a page, the front page, ...
 */
class Promotion
{
    /**
     * Promotion constructor.
     *
     * @param int            $priority
     * @param Timestamp|null $endDate
     */
    public function __construct(
        private int $priority = 0,
        private ?Timestamp $endDate = null
    ) {
    }

    /**
     * Is this promotion enabled?
     */
    public function isEnabled(): bool
    {
        return $this->priority > 0;
    }

    /**
     * Returns the end date of the promotion
     */
    public function getEndDate(): ?Timestamp
    {
        return $this->endDate;
    }

    /**
     * Get the priority
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Is this promotion active?
     */
    public function isActive()
    {
        if ($this->priority > 0) {
            if ($this->endDate == null) {
                return true;
            }
            return !$this->endDate->isPast();
        }
        return false;
    }
}

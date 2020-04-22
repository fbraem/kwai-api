<?php
/**
 * @package Kwai
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
     * Is this promotion active?
     */
    private bool $enabled;

    /**
     * When does the promotion end?
     */
    private ?Timestamp $endDate;

    /**
     * Promotion constructor.
     *
     * @param bool           $enabled
     * @param Timestamp|null $endDate
     */
    public function __construct(
        bool $enabled = false,
        Timestamp $endDate = null
    ) {
        $this->enabled = $enabled;
        $this->endDate = $endDate;
    }

    /**
     * Is this promotion enabled?
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Returns the end date of the promotion
     */
    public function getEndDate(): ?Timestamp
    {
        return $this->endDate;
    }

    /**
     * Is this promotion active?
     */
    public function isActive()
    {
        if ($this->enabled) {
            if ($this->endDate == null) {
                return true;
            }
            return !$this->endDate->isPast();
        }
        return false;
    }
}

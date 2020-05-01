<?php
/**
 * TraceableTime class
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\ValueObjects;

/**
  * Value object that handles the created_at and updated_at timestamps.
  */
final class TraceableTime
{
    /**
     * Time of creation
     */
    private Timestamp $created_at;

    /**
     * Time of last update
     */
    private ?Timestamp $updated_at;

    /**
     * Constructor
     *
     * @param Timestamp $created_at The timestamp of creation
     * @param Timestamp $updated_at The timestamp of the last modification
     */
    public function __construct(
        Timestamp $created_at = null,
        Timestamp $updated_at = null
    ) {
        $this->created_at = $created_at ?? Timestamp::createNow();
        $this->updated_at = $updated_at;
    }

    /**
     * Returns the timestamp of creation.
     * @return Timestamp
     */
    public function getCreatedAt(): Timestamp
    {
        return $this->created_at;
    }

    /**
     * Returns the timestamp of the last modification
     * @return Timestamp|null
     */
    public function getUpdatedAt(): ?Timestamp
    {
        return $this->updated_at;
    }

    /**
     * Check if there was an update.
     * @return bool
     */
    public function isUpdated(): bool
    {
        return $this->updated_at != null;
    }

    /**
     * Change the updateAt to the current timestamp
     */
    public function markUpdated(): void
    {
        $this->updated_at = Timestamp::createNow();
    }
}
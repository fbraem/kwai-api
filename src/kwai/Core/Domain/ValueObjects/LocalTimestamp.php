<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

/**
 * Class LocalTimestamp
 *
 * A timestamp, that also stores the originated timezone. Note, that the
 * passed timestamp should always be UTC.
 */
class LocalTimestamp
{
    public function __construct(
        private Timestamp $timestamp,
        private string $timezone
    ) {
    }

    /**
     * @return Timestamp
     */
    public function getTimestamp(): Timestamp
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Factory method.
     * It will convert the passed datetime from the given
     * timezone to UTC.
     *
     * @param string $datetime
     * @param string $timezone
     * @return static
     */
    public static function createFromString(
        string $datetime,
        string $timezone
    ): self {
        return new LocalTimestamp(
            Timestamp::createFromString($datetime, $timezone),
            $timezone
        );
    }
}

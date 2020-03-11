<?php
/**
 * Class UniqueId
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain;

use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\UuidInterface;

/**
 * Valueobject for a unique id (uuid).
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class UniqueId
{
    /**
     * A version 4 unique id.
     */
    private UuidInterface $id;

    /**
     * Creates a UniqueId instance.
     *
     * When an id is passed, it will be checked for validation. If no id is
     * passed, a new UUID4 will be generated.
     *
     * @param string|null $id
     * @throws InvalidArgumentException Thrown when $id is invalid.
     * @throws Exception
     */
    public function __construct(string $id = null)
    {
        if ($id) {
            try {
                $this->id = Uuid::fromString($id);
                if ($this->id->getVersion() !== Uuid::UUID_TYPE_RANDOM) {
                    throw new InvalidArgumentException('UUID must be v4');
                }
            } catch (InvalidUuidStringException $e) {
                throw new InvalidArgumentException('UUID is not valid');
            }
        } else {
            $this->id = Uuid::uuid4();
        }
    }

    public function __toString()
    {
        return $this->id->toString();
    }
}

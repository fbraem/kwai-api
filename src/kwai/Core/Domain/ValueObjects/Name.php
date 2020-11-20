<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

/**
 * Class Name
 *
 * A value object for a name.
 */
class Name
{
    private ?string $first_name;

    private ?string $last_name;

    /**
     * Name constructor.
     *
     * @param string|null $first_name
     * @param string|null $last_name
     */
    public function __construct(?string $first_name = null, ?string $last_name = null)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function __toString() : string
    {
        return implode(
            ' ',
            array_filter([$this->first_name, $this->last_name])
        );
    }
}

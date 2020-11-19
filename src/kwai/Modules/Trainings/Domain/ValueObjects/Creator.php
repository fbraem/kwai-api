<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\ValueObjects;

/**
 * Class Creator
 *
 * Value object for a creator.
 */
class Creator
{
    private int $id;

    private string $name;

    /**
     * Creator constructor.
     *
     * @param int    $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Returns the id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the name
     */
    public function getName(): string
    {
        return $this->name;
    }
}

<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\ValueObjects;

/**
 * Class TrainingDefinition
 *
 * Value object for a TrainingDefinition entity.
 */
class TrainingDefinition
{
    /**
     * The id of the definition
     */
    private int $id;

    /**
     * The name of the definition
     */
    private string $name;

    /**
     * TrainingDefinition constructor.
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}

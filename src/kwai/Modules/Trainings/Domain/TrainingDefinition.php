<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;

/**
 * Class TrainingDefinition
 *
 * Value object for a TrainingDefinition entity.
 */
class TrainingDefinition implements DomainEntity
{
    /**
     * The name of the definition
     */
    private string $name;

    /**
     * TrainingDefinition constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->name = $props->name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}

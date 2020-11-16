<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;

/**
 * Class Creator
 *
 * Entity class for a creator. The user that created an entity in the
 * Trainings module
 */
class Creator implements DomainEntity
{
    private string $name;

    /**
     * Creator constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->name = $props->name;
    }

    /**
     * Returns the name
     */
    public function getName(): string
    {
        return $this->name;
    }
}

<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\TraceableTime;

/**
 * Class Coach
 *
 * Entity that represents a Coach
 */
class Coach implements DomainEntity
{
    /**
     * The firstname of the coach
     */
    private string $firstname;

    /**
     * The lastname of the coach
     */
    private string $lastname;

    /**
     * Coach constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->firstname = $props->firstname;
        $this->lastname = $props->lastname;
    }

    /**
     * Get the firstname of the coach
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * Get the lastname of the coach
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }
}

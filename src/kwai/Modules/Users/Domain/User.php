<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\UniqueId;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

/**
 * User Entity
 */
class User implements DomainEntity
{
    /**
     * A UUID of the user.
     */
    private UniqueId $uuid;

    /**
     * The email address of the user
     */
    private EmailAddress $emailAddress;

    /**
     * Track create & modify times
     */
    private TraceableTime $traceableTime;

    /**
     * A remark about the user
     */
    private ?string $remark;

    /**
     * The username
     */
    private ?Username $username;

    /**
     * The abilities of the user.
     * @var Ability[]
     */
    private array $abilities;

    /**
     * Constructor.
     * @param  object $props User properties
     */
    public function __construct(object $props)
    {
        $this->uuid = $props->uuid;
        $this->emailAddress = $props->emailAddress;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
        $this->remark = $props->remark ?? null;
        $this->username = $props->username ?? null;
        $this->abilities = $props->abilities ?? [];
    }

    /**
     * Return the abilities of this user.
     * @return Ability[]
     */
    public function getAbilities(): array
    {
        return $this->abilities;
    }

    /**
     * Returns the email address.
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * Get the created_at/updated_at timestamps
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Get the remark
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Get the unique id of the user
     * @return UniqueId
     */
    public function getUuid(): UniqueId
    {
        return $this->uuid;
    }

    /**
     * Get the username
     * @return Username|null ?Username
     */
    public function getUsername(): ?Username
    {
        return $this->username;
    }

    /**
     * Adds an ability to this user.
     * @param Entity<Ability> $ability
     */
    public function addAbility(Entity $ability)
    {
        $this->abilities[$ability->id()] = $ability;
    }

    /**
     * Set the abilities of the user
     * @param array $abilities
     */
    public function setAbilities(array $abilities)
    {
        $this->abilities = [];
        foreach ($abilities as $ability) {
            $this->addAbility($ability);
        }
    }

    /**
     * Removes the ability from the user.
     *
     * @param Entity $ability
     */
    public function removeAbility(Entity $ability)
    {
        unset($this->abilities[$ability->id()]);
    }
}

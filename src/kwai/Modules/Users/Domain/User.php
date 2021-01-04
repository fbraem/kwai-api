<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;

/**
 * User Entity
 */
class User implements DomainEntity
{
    /**
     * Constructor.
     *
     * @param UniqueId           $uuid
     * @param EmailAddress       $emailAddress
     * @param Name               $username
     * @param Collection|null    $abilities
     * @param string|null        $remark
     * @param int|null           $member
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private UniqueId $uuid,
        private EmailAddress $emailAddress,
        private Name $username,
        private ?Collection $abilities = null,
        private ?string $remark = null,
        private ?int $member = null,
        private ?TraceableTime $traceableTime = null
    ) {
        $this->traceableTime ??= new TraceableTime();
        $this->abilities ??= collect();
    }

    /**
     * Return the abilities of this user.
     *
     * @return Collection
     */
    public function getAbilities(): Collection
    {
        return $this->abilities->collect();
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
     * @return Name Username
     */
    public function getUsername(): Name
    {
        return $this->username;
    }

    /**
     * Adds an ability to this user.
     * @param Entity<Ability> $ability
     */
    public function addAbility(Entity $ability)
    {
        $this->abilities->put($ability->id(), $ability);
    }

    /**
     * Set the abilities of the user
     *
     * @param Collection $abilities
     */
    public function setAbilities(Collection $abilities)
    {
        $this->abilities = $abilities->collect();
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

    /**
     * Get the id of the associated member (if any)
     *
     * @return int|null
     */
    public function getMember(): ?int
    {
        return $this->member;
    }
}

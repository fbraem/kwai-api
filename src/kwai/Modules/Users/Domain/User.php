<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Permission;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;

/**
 * User Entity
 */
final class User
{
    /**
     * Constructor.
     *
     * @param UniqueId               $uuid
     * @param EmailAddress           $emailAddress
     * @param Name                   $username
     * @param bool                   $admin
     * @param Collection<RoleEntity> $roles
     * @param string                 $remark
     * @param int|null               $member
     * @param TraceableTime          $traceableTime
     */
    public function __construct(
        private readonly UniqueId      $uuid,
        private readonly EmailAddress  $emailAddress,
        private readonly Name          $username,
        private bool                   $admin = false,
        private readonly Collection    $roles = new Collection(),
        private readonly string        $remark = '',
        private readonly ?int          $member = null,
        private readonly TraceableTime $traceableTime = new TraceableTime()
    ) {
    }

    /**
     * Return the roles of this user.
     *
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles->collect();
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
     * @return string
     */
    public function getRemark(): string
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
     * Adds a role to this user.
     *
     * @param RoleEntity $role
     */
    public function addRole(RoleEntity $role)
    {
        $this->roles->put($role->id(), $role);
    }

    /**
     * Removes the role from the user.
     *
     * @param RoleEntity $role
     */
    public function removeRole(RoleEntity $role)
    {
        unset($this->roles[$role->id()]);
    }

    /**
     * Checks if the user has the given role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains(
            fn(RoleEntity $role) => $role->getName() === $roleName
        );
    }

    /**
     * Returns true when the user has the permission for the given subject.
     *
     * @param string     $subject
     * @param Permission $permission
     * @return bool
     */
    public function hasPermission(string $subject, Permission $permission): bool
    {
        $allRules = collect([]);
        foreach($this->roles as $role) {
            $allRules = $allRules->merge($role->getRules());
        }
        $permissions = $allRules->filter(
            fn(RuleEntity $rule) =>
                in_array($rule->getSubject(), ['all', $subject]) && $rule->hasPermission($permission)
        );
        return $permissions->isNotEmpty();
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

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }
}

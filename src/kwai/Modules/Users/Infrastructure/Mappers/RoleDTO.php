<?php
/**
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Infrastructure\RolesTable;

final class RoleDTO
{
    /**
     * @param RolesTable          $role
     * @param Collection<RuleDTO> $rules
     */
    public function __construct(
        public RolesTable $role = new RolesTable(),
        public Collection $rules = new Collection()
    ) {
    }

    /**
     * Create an Role domain object from a database row.
     *
     * @return Role
     */
    public function create(): Role
    {
        return new Role(
            name: $this->role->name,
            remark: $this->role->remark,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->role->created_at),
                $this->role->updated_at
                    ? Timestamp::createFromString($this->role->updated_at)
                    : null
            ),
            rules: $this->rules
                ->map(
                    fn (RuleDTO $ruleDto) => $ruleDto->createEntity()
                )
        );
    }

    /**
     * Create a Role entity from a database row
     *
     * @return Entity<Role>
     */
    public function createEntity(): Entity
    {
        return new Entity(
            $this->role->id,
            $this->create()
        );
    }

    /**
     * Persist the Role domain object to a database row.
     *
     * @param Role $role
     * @return RoleDTO
     */
    public function persist(Role $role): RoleDTO
    {
        $this->role->name = $role->getName();
        $this->role->remark = $role->getRemark();
        $this->role->created_at = (string) $role->getTraceableTime()->getCreatedAt();
        if ($role->getTraceableTime()->isUpdated()) {
            $this->role->updated_at = (string) $role->getTraceableTime()->getUpdatedAt();
        }
        $this->rules = $role->getRules()->map(
            static fn(Entity $rule) => (new RuleDTO())->persistEntity($rule)
        );
        return $this;
    }

    /**
     * Persist the Role entity to a database row.
     *
     * @param Entity<Role> $role
     * @return $this
     */
    public function persistEntity(Entity $role): RoleDTO
    {
        $this->role->id = $role->id();
        return $this->persist($role->domain());
    }
}

<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;
use Kwai\Modules\Users\Domain\RoleEntity;
use Kwai\Modules\Users\Repositories\RoleRepository;
use Kwai\Modules\Users\Repositories\RuleRepository;

/**
 * Class UpdateRole
 *
 * Use case to update a role
 */
class UpdateRole
{
    /**
     * UpdateRole constructor.
     *
     * @param RoleRepository $roleRepo
     * @param RuleRepository $ruleRepo
     */
    public function __construct(
        private RoleRepository $roleRepo,
        private RuleRepository $ruleRepo
    ) {
    }

    /**
     * Factory method
     *
     * @param RoleRepository $roleRepo
     * @param RuleRepository $ruleRepo
     * @return UpdateRole
     */
    public static function create(
        RoleRepository $roleRepo,
        RuleRepository $ruleRepo
    ): self {
        return new self($roleRepo, $ruleRepo);
    }

    /**
     * @param UpdateRoleCommand $command
     * @return RoleEntity
     * @throws RepositoryException
     * @throws RoleNotFoundException
     */
    public function __invoke(UpdateRoleCommand $command): RoleEntity
    {
        $role = $this->roleRepo->getById($command->id);
        if (count($command->rules) > 0) {
            $newRules = $this->ruleRepo->getByIds($command->rules);
        } else {
            $newRules = collect();
        }

        $traceableTime = $role->getTraceableTime();
        $traceableTime->markUpdated();

        $role = new RoleEntity(
            $role->id(),
            new Role(
                name: $command->name,
                remark: $command->remark,
                traceableTime: $traceableTime,
                rules: $newRules
            )
        );
        $this->roleRepo->update($role);

        return $role;
    }
}

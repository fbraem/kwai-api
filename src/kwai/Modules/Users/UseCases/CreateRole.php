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
use Kwai\Modules\Users\Repositories\RoleRepository;
use Kwai\Modules\Users\Repositories\RuleRepository;

/**
 * Class CreateRole
 *
 * Use case to create a new role
 */
class CreateRole
{
    /**
     * CreateRole constructor.
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
     * @return static
     */
    public static function create(RoleRepository $roleRepo, RuleRepository $ruleRepo): self
    {
        return new self($roleRepo, $ruleRepo);
    }

    /**
     * @param CreateRoleCommand $command
     * @return Entity<Role>
     * @throws RepositoryException
     */
    public function __invoke(CreateRoleCommand $command): Entity
    {
        if (count($command->rules) > 0) {
            $rules = $this->ruleRepo->getbyIds($command->rules);
        } else {
            $rules = collect();
        }
        return $this->roleRepo->create(
            new Role(
                name: $command->name,
                remark: $command->remark,
                rules:  $rules
            )
        );
    }
}

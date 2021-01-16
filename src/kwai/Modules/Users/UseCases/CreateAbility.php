<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\RuleRepository;

/**
 * Class CreateAbility
 *
 * Use case to create a new ability
 */
class CreateAbility
{
    /**
     * CreateAbility constructor.
     *
     * @param AbilityRepository $abilityRepo
     * @param RuleRepository $ruleRepo
     */
    public function __construct(
        private AbilityRepository $abilityRepo,
        private RuleRepository $ruleRepo
    ) {
    }

    /**
     * Factory method
     *
     * @param AbilityRepository $abilityRepo
     * @param RuleRepository    $ruleRepo
     * @return static
     */
    public static function create(AbilityRepository $abilityRepo, RuleRepository $ruleRepo): self
    {
        return new self($abilityRepo, $ruleRepo);
    }

    /**
     * @param CreateAbilityCommand $command
     * @return Entity<Ability>
     * @throws RepositoryException
     */
    public function __invoke(CreateAbilityCommand $command): Entity
    {
        $rules = $this->ruleRepo->getbyIds($command->rules);
        return $this->abilityRepo->create(
            new Ability(
                name: $command->name,
                remark: $command->remark,
                rules:  $rules
            )
        );
    }
}

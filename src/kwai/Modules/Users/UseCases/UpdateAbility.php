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
use Kwai\Modules\Users\Domain\Exceptions\AbilityNotFoundException;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\RuleRepository;

/**
 * Class UpdateAbility
 *
 * Use case to update an ability
 */
class UpdateAbility
{
    /**
     * UpdateAbility constructor.
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
     * @return UpdateAbility
     */
    public static function create(
        AbilityRepository $abilityRepo,
        RuleRepository $ruleRepo
    ): self {
        return new self($abilityRepo, $ruleRepo);
    }

    /**
     * @param UpdateAbilityCommand $command
     * @return Entity<Ability>
     * @throws RepositoryException
     * @throws AbilityNotFoundException
     */
    public function __invoke(UpdateAbilityCommand $command): Entity
    {
        $ability = $this->abilityRepo->getById($command->id);
        $newRules = $this->ruleRepo->getByIds($command->rules);

        $traceableTime = $ability->getTraceableTime();
        $traceableTime->markUpdated();

        $ability = new Entity(
            $ability->id(),
            new Ability(
                name: $command->name,
                remark: $command->remark,
                traceableTime: $traceableTime,
                rules: $newRules
            )
        );
        $this->abilityRepo->update($ability);

        return $ability;
    }
}

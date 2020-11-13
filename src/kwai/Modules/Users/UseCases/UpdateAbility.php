<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
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
     * The ability repository
     */
    private AbilityRepository $abilityRepo;

    /**
     * The rule repository
     */
    private RuleRepository $ruleRepo;

    /**
     * UpdateAbility constructor.
     *
     * @param AbilityRepository $abilityRepo
     * @param RuleRepository $ruleRepo
     */
    public function __construct(AbilityRepository $abilityRepo, RuleRepository $ruleRepo)
    {
        $this->abilityRepo = $abilityRepo;
        $this->ruleRepo = $ruleRepo;
    }

    /**
     * @param UpdateAbilityCommand $command
     * @return Entity<Ability>
     * @throws RepositoryException
     * @throws NotFoundException
     */
    public function __invoke(UpdateAbilityCommand $command): Entity
    {
        $ability = $this->abilityRepo->getById($command->id);
        $newRules = $this->ruleRepo->getByIds($command->rules);
        $currentRules = $ability->getRules();

        // All rules that are not passed, must be deleted
        $rulesToDelete = array_udiff(
            $currentRules,
            $newRules,
            fn($a, $b) => $a->id() == $b->id() ? 0 : -1
        );
        $this->abilityRepo->deleteRules($ability, $rulesToDelete);
        // Remove the deleted rules from the list
        $currentRules = array_udiff(
            $currentRules,
            $rulesToDelete,
            fn($a, $b) => $a->id() == $b->id() ? 0 : -1
        );

        $rulesToAdd = array_udiff(
            $newRules,
            $currentRules,
            fn($a, $b) => $a->id() == $b->id() ? 0 : -1
        );
        $this->abilityRepo->addRules($ability, $rulesToAdd);
        foreach ($rulesToAdd as $rule) {
            $currentRules[$rule->id()] = $rule;
        }

        $traceableTime = $ability->getTraceableTime();
        $traceableTime->markUpdated();
        $ability = new Entity(
            $ability->id(),
            new Ability((object) [
                'name' => $command->name,
                'remark' => $command->remark,
                'traceableTime' => $traceableTime,
                'rules' => $currentRules
            ])
        );
        $this->abilityRepo->update($ability);

        return $ability;
    }
}

<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
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
     * The ability repository
     */
    private AbilityRepository $abilityRepo;

    /**
     * The rule repository
     */
    private RuleRepository $ruleRepo;

    /**
     * CreateAbility constructor.
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
     * @param CreateAbilityCommand $command
     * @return Entity<Ability>
     */
    public function __invoke(CreateAbilityCommand $command): Entity
    {
        $rules = $this->ruleRepo->getbyIds($command->rules);
        $this->abilityRepo->create(
          new Ability((object) [
              'name' => $command->name,
              'remark' => $command->remark,
              'rules' =>  $rules
          ])
        );
    }
}

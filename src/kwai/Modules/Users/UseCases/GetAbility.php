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

/**
 * Class GetAbility
 *
 * Use case to get an ability with the given id.
 */
class GetAbility
{
    /**
     * GetAbility constructor.
     *
     * @param AbilityRepository $repo
     */
    public function __construct(private AbilityRepository $repo)
    {
    }

    /**
     * Factory method
     *
     * @param AbilityRepository $repo
     * @return static
     */
    public static function create(AbilityRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * Get an ability
     *
     * @param GetAbilityCommand $command
     * @return Entity<Ability>
     * @throws RepositoryException
     * @throws AbilityNotFoundException
     */
    public function __invoke(GetAbilityCommand $command): Entity
    {
        return $this->repo->getByID($command->id);
    }
}

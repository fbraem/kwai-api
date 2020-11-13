<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Repositories\AbilityRepository;

/**
 * Class GetAbility
 *
 * Use case to get an ability with the given id.
 */
class GetAbility
{
    private AbilityRepository $repo;

    public function __construct(AbilityRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get a user
     *
     * @param GetAbilityCommand $command
     * @return Entity<Ability>
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function __invoke(GetAbilityCommand $command): Entity
    {
        return $this->repo->getByID($command->id);
    }
}

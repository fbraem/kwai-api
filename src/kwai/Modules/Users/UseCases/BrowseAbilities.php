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

/**
 * Class BrowseAbilities
 *
 * Use case for browsing abilities
 */
class BrowseAbilities
{
    private AbilityRepository $repo;

    /**
     * BrowseAbilities constructor.
     *
     * @param AbilityRepository $repo
     */
    public function __construct(AbilityRepository $repo)
    {
        $this->repo = $repo;
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
     * Get all abilities.
     *
     * @param BrowseAbilitiesCommand $command
     * @return Entity<Ability>[]
     * @throws RepositoryException
     */
    public function __invoke(BrowseAbilitiesCommand $command): array
    {
        return $this->repo->getAll();
    }
}

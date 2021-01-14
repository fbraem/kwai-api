<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Repositories\AbilityRepository;

/**
 * Class BrowseAbilities
 *
 * Use case for browsing abilities
 */
class BrowseAbilities
{
    /**
     * BrowseAbilities constructor.
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
     * Get all abilities.
     *
     * @param BrowseAbilitiesCommand $command
     * @return array
     * @throws RepositoryException
     * @throws QueryException
     */
    public function __invoke(BrowseAbilitiesCommand $command): array
    {
        $query = $this->repo->createQuery();
        return [
            $query->count(),
            $this->repo->getAll($query)
        ];
    }
}

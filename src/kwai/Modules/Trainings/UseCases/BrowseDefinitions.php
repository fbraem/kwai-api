<?php
/**
 * @package Module
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;

/**
 * Class BrowseDefinitions
 *
 * Use case for browsing training definitions.
 */
final class BrowseDefinitions
{
    /**
     * BrowseDefinitions constructor.
     *
     * @param DefinitionRepository $repo
     */
    public function __construct(
        private readonly DefinitionRepository $repo
    ) {
    }

    /**
     * Factory method.
     *
     * @param DefinitionRepository $repo
     * @return BrowseDefinitions
     */
    public static function create(DefinitionRepository $repo): BrowseDefinitions
    {
        return new self($repo);
    }

    /**
     * Browse definitions
     *
     * @param BrowseDefinitionsCommand $command
     * @return array
     * @throws QueryException
     * @throws RepositoryException
     */
    public function __invoke(BrowseDefinitionsCommand $command): array
    {
        $query = $this->repo->createQuery();

        $count = $query->count();
        $definitions = $this->repo->getAll(
            $query,
            $command->limit,
            $command->offset
        );

        return [
            $count,
            $definitions
        ];
    }
}

<?php
/**
 * @package Module
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;

/**
 * Class BrowseDefinitions
 *
 * Use case for browsing training definitions.
 */
class BrowseDefinitions
{
    /**
     * BrowseDefinitions constructor.
     *
     * @param DefinitionRepository $repo
     */
    public function __construct(
        private DefinitionRepository $repo
    ) {
    }

    /**
     * Factory method.
     *
     * @param DefinitionRepository $repo
     * @return BrowseDefinitions
     */
    public static function create(DefinitionRepository $repo)
    {
        return new self($repo);
    }

    /**
     * Browse definitions
     *
     * @param BrowseDefinitionsCommand $command
     * @return array
     * @throws QueryException
     */
    public function __invoke(BrowseDefinitionsCommand $command)
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

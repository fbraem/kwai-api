<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;

/**
 * Class GetDefinition
 *
 * Use case for getting a definition
 */
class GetDefinition
{
    /**
     * GetDefinition constructor.
     *
     * @param DefinitionRepository $repo
     */
    public function __construct(
        private DefinitionRepository $repo
    ) {
    }

    /**
     * Factory method to create this use case.
     *
     * @param DefinitionRepository $repo
     * @return GetDefinition
     */
    public static function create(DefinitionRepository $repo): GetDefinition
    {
        return new self($repo);
    }

    /**
     * @param GetDefinitionCommand $command
     * @return Entity<Definition>
     * @throws DefinitionNotFoundException
     * @throws RepositoryException
     */
    public function __invoke(GetDefinitionCommand $command): Entity
    {
        return $this->repo->getById($command->id);
    }
}

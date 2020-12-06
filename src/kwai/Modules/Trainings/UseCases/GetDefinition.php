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
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingDefinitionNotFoundException;
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
    public static function create(DefinitionRepository $repo) {
        return new self($repo);
    }

    /**
     * @param GetDefinitionCommand $command
     * @throws RepositoryException
     * @throws TrainingDefinitionNotFoundException
     * @return Entity<Definition>
     */
    public function __invoke(GetDefinitionCommand $command)
    {
        return $this->repo->getById($command->id);
    }
}

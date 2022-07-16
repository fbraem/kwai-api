<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\DefinitionEntity;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;

/**
 * Class GetDefinition
 *
 * Use case for getting a definition
 */
final class GetDefinition
{
    /**
     * GetDefinition constructor.
     *
     * @param DefinitionRepository $repo
     */
    public function __construct(
        private readonly DefinitionRepository $repo
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
     * @return DefinitionEntity
     * @throws DefinitionNotFoundException
     * @throws RepositoryException
     */
    public function __invoke(GetDefinitionCommand $command): DefinitionEntity
    {
        return $this->repo->getById($command->id);
    }
}

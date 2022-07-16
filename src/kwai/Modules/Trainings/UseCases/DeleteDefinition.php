<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;

/**
 * Class DeleteDefinition
 *
 * Use case for deleting a definition
 */
final class DeleteDefinition
{
    /**
     * DeleteDefinition constructor.
     *
     * @param DefinitionRepository $repo
     */
    public function __construct(
        private readonly DefinitionRepository $repo
    ) {
    }

    /**
     * Factory method
     *
     * @param DefinitionRepository $repo
     * @return DeleteDefinition
     */
    public static function create(DefinitionRepository $repo): DeleteDefinition
    {
        return new self($repo);
    }

    /**
     * Executes the use case
     *
     * @param DeleteDefinitionCommand $command
     * @throws RepositoryException
     * @throws DefinitionNotFoundException
     */
    public function __invoke(DeleteDefinitionCommand $command): void
    {
        $definition = $this->repo->getById($command->id);
        $this->repo->remove($definition);
    }
}

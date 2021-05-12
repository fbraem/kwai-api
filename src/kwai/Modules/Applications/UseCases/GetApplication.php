<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Application;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\Repositories\ApplicationRepository;

/**
 * Class GetApplication
 *
 * Use case: get an application
 */
class GetApplication
{
    private ApplicationRepository $repo;

    /**
     * BrowseApplication constructor.
     *
     * @param ApplicationRepository $repo
     */
    public function __construct(ApplicationRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Factory method
     *
     * @param ApplicationDatabaseRepository $repo
     * @return static
     */
    public static function create(ApplicationDatabaseRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * @param GetApplicationCommand $command
     * @return Entity<Application>
     * @throws RepositoryException
     * @throws ApplicationNotFoundException
     */
    public function __invoke(GetApplicationCommand $command)
    {
        return $this->repo->getById($command->id);
    }
}

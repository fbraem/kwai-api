<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Application;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Applications\Repositories\ApplicationRepository;

/**
 * Class UpdateApplication
 *
 * Use case: update an application
 */
class UpdateApplication
{
    /**
     * UpdateApplication constructor.
     *
     * @param ApplicationRepository $repo
     */
    public function __construct(
        private ApplicationRepository $repo
    ) {
    }

    /**
     * Factory method
     *
     * @param ApplicationRepository $repo
     * @return UpdateApplication
     */
    public static function create(ApplicationRepository $repo)
    {
        return new self($repo);
    }

    /**
     * @param UpdateApplicationCommand $command
     * @param Creator                  $creator
     * @throws RepositoryException
     * @throws QueryException
     * @throws ApplicationNotFoundException
     */
    public function __invoke(UpdateApplicationCommand $command, Creator $creator)
    {
        $application = $this->repo->getById($command->id);

        /** @noinspection PhpUndefinedMethodInspection */
        $traceableTime = $application->getTraceableTime();
        $traceableTime->markUpdated();

        /** @noinspection PhpUndefinedMethodInspection */
        $application = new Entity(
            $application->id(),
            new Application(
                name: $application->getName(),
                title: $command->title,
                description: $command->description,
                shortDescription: $command->short_description,
                remark: $application->getRemark(),
                traceableTime: $traceableTime,
                canHaveNews: $application->canHaveNews(),
                canHavePages: $application->canHavePages(),
                canHaveEvents: $application->canHaveEvents()
            )
        );
        $this->repo->update($application);

        return $application;
    }
}

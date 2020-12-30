<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Coaches\Repositories\CoachRepository;

/**
 * Class BrowseCoaches
 *
 * Use case Browse Coaches
 */
class BrowseCoaches
{
    /**
     * BrowseCoaches constructor.
     *
     * @param CoachRepository $repo
     */
    public function __construct(
        private CoachRepository $repo
    ) {
    }

    /**
     * Factory method to create this use case.
     *
     * @param CoachRepository $repo
     * @return static
     */
    public static function create(CoachRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * @param BrowseCoachesCommand $command
     * @return array
     * @throws QueryException
     */
    public function __invoke(BrowseCoachesCommand $command)
    {
        $query = $this->repo->createQuery();
        if ($command->active) {
            $query->filterActive($command->active);
        }

        return [
            $query->count(),
            $coaches = $this->repo->getAll($query)
        ];
    }
}

<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Club\Repositories\MemberRepository;

/**
 * Class BrowseMembers
 */
class BrowseMembers
{
    public function __construct(
        private MemberRepository $repo
    ) {
    }

    public static function create(MemberRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * @throws RepositoryException
     * @throws QueryException
     */
    public function __invoke(BrowseMembersCommand $command)
    {
        $query = $this->repo->createQuery();

        return [
            $query->count(),
            $this->repo->getAll($query)
        ];
    }
}

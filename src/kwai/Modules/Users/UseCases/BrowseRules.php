<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Repositories\RuleRepository;

/**
 * BrowseRules
 *
 * Use case for browsing rules
 */
class BrowseRules
{
    /**
     * BrowseRules constructor.
     *
     * @param RuleRepository $repo
     */
    public function __construct(private RuleRepository $repo)
    {
    }

    /**
     * Factory method
     *
     * @param RuleRepository $repo
     * @return BrowseRules
     */
    public static function create(RuleRepository $repo)
    {
        return new self($repo);
    }

    /**
     * Get all abilities.
     *
     * @param BrowseRulesCommand $command
     * @return array[int, array[Entity<Rule>]
     * @throws QueryException
     * @throws RepositoryException
     */
    public function __invoke(BrowseRulesCommand $command): array
    {
        $query = $this->repo->createQuery();
        if ($command->subject) {
            $query->filterBySubject($command->subject);
        }
        return [
            $query->count(),
            $this->repo->getAll($query)
        ];
    }
}

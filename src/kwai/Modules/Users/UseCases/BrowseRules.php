<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Repositories\RuleRepository;

/**
 * BrowseRules
 */
class BrowseRules
{
    private RuleRepository $repo;

    /**
     * BrowseRules constructor.
     *
     * @param RuleRepository $repo
     */
    public function __construct(RuleRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get all abilities.
     *
     * @param BrowseRulesCommand $command
     * @return Entity<Rule>[]
     * @throws RepositoryException
     */
    public function __invoke(BrowseRulesCommand $command): array
    {
        return $this->repo->getAll($command->subject);
    }
}

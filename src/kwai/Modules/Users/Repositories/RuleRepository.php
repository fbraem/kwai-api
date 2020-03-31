<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);


namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Rule;

interface RuleRepository
{
    /**
     * Get all rules
     *
     * @param string|null $subject
     * @throws RepositoryException
     * @return Entity<Rule>[]
     */
    public function getAll(?string $subject = null): array;

    /**
     * Get all rules with the given ids. The id is used
     * as key of the returned array.
     *
     * @param int[] $ids
     * @throws RepositoryException
     * @return Entity<Rule>[]
     */
    public function getByIds(array $ids): array;
}

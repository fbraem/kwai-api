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
}

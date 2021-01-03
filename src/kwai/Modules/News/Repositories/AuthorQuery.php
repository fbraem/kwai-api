<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface AuthorQuery
 *
 * Interface for querying authors
 */
interface AuthorQuery extends Query
{
    /**
     * Filter for the given ids
     *
     * @param int ...$id
     * @return $this
     */
    public function filterIds(int ...$id): self;

    /**
     * Filter for the given uniqued id
     *
     * @param UniqueId $id
     * @return $this
     */
    public function filterUniqueId(UniqueId $id): self;
}

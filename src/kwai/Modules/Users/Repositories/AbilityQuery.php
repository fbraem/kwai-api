<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface AbilityQuery
 */
interface AbilityQuery extends Query
{
    /**
     * Filter by id of the ability
     *
     * @param int $id
     * @return $this
     */
    public function filterById(int $id): self;
}

<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface RuleQuery
 */
interface RuleQuery extends Query
{
    /**
     * Filter on the given id(s)
     *
     * @param int ...$id
     * @return $this
     */
    public function filterById(int ...$id): self;

    /**
     * Filter all rules of the subject
     *
     * @param string $subject
     * @return $this
     */
    public function filterBySubject(string $subject): self;
}

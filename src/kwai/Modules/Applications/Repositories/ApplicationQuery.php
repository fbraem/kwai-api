<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface ApplicationQuery
 */
interface ApplicationQuery extends Query
{
    /**
     * Filter on id
     *
     * @param int $id
     * @return $this
     */
    public function filterId(int $id): self;

    /**
     * Filter on application
     *
     * @param string $application
     * @return $this
     */
    public function filterApplication(string $application): self;
}

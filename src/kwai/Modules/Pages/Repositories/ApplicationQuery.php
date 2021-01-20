<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Repositories;

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
     * @return ApplicationQuery
     */
    public function filterId(int $id): self;
}

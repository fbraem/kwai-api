<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface TeamQuery
 */
interface TeamQuery extends Query
{
    public function filterId(int $id): self;
}

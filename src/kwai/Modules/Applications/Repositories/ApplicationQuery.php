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
    public function filterId(int $id): void;

    public function filterApplication(string $application);
}

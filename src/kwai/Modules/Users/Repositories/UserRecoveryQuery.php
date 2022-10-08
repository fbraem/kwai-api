<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface UserRecoveryQuery
 */
interface UserRecoveryQuery extends Query
{
    public function filterById(int $id): self;

    public function filterByUUID(UniqueId $uuid): self;
}
<?php
/**
 * User Repository interface
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\UniqueId;
use Kwai\Modules\Users\Domain\User;

/**
 * User repository interface
 */
interface UserRepository
{
    public function getById(int $id) : User;
    public function getByUUID(UniqueId $uid) : User;
}

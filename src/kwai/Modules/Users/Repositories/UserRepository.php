<?php
/**
 * User Repository interface
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
 * User repository interface
 */
interface UserRepository
{
    public function getById(int $id) : Entity;
    public function getByUUID(UniqueId $uid) : Entity;
    public function getByAccessToken(TokenIdentifier $token) : Entity;
}

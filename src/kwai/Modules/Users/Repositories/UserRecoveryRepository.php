<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserRecoveryNotFoundException;
use Kwai\Modules\Users\Domain\UserRecovery;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;

/**
 * Interface UserRecoveryRepository
 */
interface UserRecoveryRepository
{
    /**
     * @throws RepositoryException
     * @throws UserRecoveryNotFoundException
     */
    public function getByUniqueId(UniqueId $uuid): UserRecoveryEntity;

    public function getAll(
        ?UserRecoveryQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;

    public function createQuery(): UserRecoveryQuery;

    public function create(UserRecovery $recovery): UserRecoveryEntity;
}
<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\AccessTokenEntity;

/**
 * AccessToken repository interface
 */
interface AccessTokenRepository
{
    /**
     * Save a new AccessToken
     *
     * @param AccessToken $token
     * @return AccessTokenEntity
     * @throws RepositoryException
     */
    public function create(AccessToken $token): AccessTokenEntity;

    /**
     * Update the accesstoken.
     *
     * @param AccessTokenEntity $token
     * @throws RepositoryException
     */
    public function update(AccessTokenEntity $token): void;

    public function createQuery(): AccessTokenQuery;
}

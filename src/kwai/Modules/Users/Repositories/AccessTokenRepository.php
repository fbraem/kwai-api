<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\AccessToken;

/**
 * AccessToken repository interface
 */
interface AccessTokenRepository
{
    /**
     * Save a new AccessToken
     * @param  AccessToken $token
     * @throws RepositoryException
     * @return Entity<AccessToken>
     */
    public function create(AccessToken $token): Entity;

    /**
     * Update the accesstoken.
     * @param  Entity<AccessToken> $token
     * @throws RepositoryException
     */
    public function update(Entity $token): void;
}

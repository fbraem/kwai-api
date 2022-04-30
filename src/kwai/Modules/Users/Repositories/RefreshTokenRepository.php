<?php
/**
 * RefreshToken Repository interface
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\RefreshTokenNotFoundException;
use Kwai\Modules\Users\Domain\RefreshTokenEntity;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\RefreshToken;

/**
 * RefreshToken repository interface
 */
interface RefreshTokenRepository
{
    /**
     * Get a refreshtoken by its token identifier.
     *
     * @param TokenIdentifier $identifier A token identifier
     * @return RefreshTokenEntity A refreshtoken
     * @throws RefreshTokenNotFoundException
     * @throws RepositoryException
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : RefreshTokenEntity;

    /**
     * Save a new RefreshToken
     *
     * @param RefreshToken $token
     * @return RefreshTokenEntity
     * @throws RepositoryException
     */
    public function create(RefreshToken $token): RefreshTokenEntity;

    /**
     * Update the refreshtoken.
     *
     * @param RefreshTokenEntity $token
     * @throws RepositoryException
     */
    public function update(RefreshTokenEntity $token): void;
}

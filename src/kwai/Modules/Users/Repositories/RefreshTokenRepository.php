<?php
/**
 * RefreshToken Repository interface
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
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
     * @param  TokenIdentifier $identifier A token identifier
     * @return Entity<RefreshToken>        A refreshtoken
     * @throws NotFoundException
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity;

    /**
     * Save a new RefreshToken
     * @param  RefreshToken $token
     * @return Entity<RefreshToken>
     */
    public function create(RefreshToken $token): Entity;

    /**
     * Update the refreshtoken.
     * @param  Entity<RefreshToken> $token
     */
    public function update(Entity $token): void;
}

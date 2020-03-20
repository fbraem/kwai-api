<?php
/**
 * AccessToken Repository interface
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\User;

/**
 * AccessToken repository interface
 */
interface AccessTokenRepository
{
    /**
     * Get an accesstoken by its token identifier.
     *
     * @param  TokenIdentifier $identifier A token identifier
     * @return Entity<AccessToken>         An accesstoken
     * @throws NotFoundException
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity;

    /**
     * Get all accesstokens of a user.
     * @param  Entity<User> $user    A user
     * @return Entity<AccessToken>[] An array with accesstokens
     */
    public function getTokensForUser(Entity $user): array;

    /**
     * Save a new AccessToken
     * @param  AccessToken $token
     * @return Entity<AccessToken>
     */
    public function create(AccessToken $token): Entity;

    /**
     * Update the accesstoken.
     * @param  Entity<AccessToken> $token
     */
    public function update(Entity $token): void;
}

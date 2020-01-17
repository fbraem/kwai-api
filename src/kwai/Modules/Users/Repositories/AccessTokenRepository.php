<?php
/**
 * AccessToken Repository interface
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
 * AccessToken repository interface
 */
interface AccessTokenRepository
{
    /**
     * Get an accesstoken by its token identifier.
     *
     * @param  TokenIdentifier $identifier A token identifier
     * @return AccessToken                 An accesstoken
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity;

    /**
     * Get all accesstokens of a user.
     * @param  User $user A user
     * @return Entity[]   An array with accesstokens
     */
    public function getTokensForUser(Entity $user): array;
}
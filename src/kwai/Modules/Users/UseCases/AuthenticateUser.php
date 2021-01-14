<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use DateTime;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;

/**
 * Usecase: Authenticate a user and create a refresh token.
 */
final class AuthenticateUser
{
    /**
     * Constructor.
     *
     * @param UserAccountRepository  $userAccountRepo
     * @param AccessTokenRepository  $accessTokenRepo
     * @param RefreshTokenRepository $refreshTokenRepo
     */
    public function __construct(
        private UserAccountRepository $userAccountRepo,
        private AccessTokenRepository $accessTokenRepo,
        private RefreshTokenRepository $refreshTokenRepo
    ) {
    }

    /**
     * Find the user by email and check the password. When the password is
     * not verified or the user is revoked an AuthenticationException will
     * be thrown. On success, a refreshtoken will be returned. This refresh
     * token will contain an access token. This access token will be
     * associated with the authenticated user.
     *
     * @param AuthenticateUserCommand $command
     * @return Entity<RefreshToken>              A RefreshToken entity
     * @throws AuthenticationException
     *    Thrown when the password is invalid, or when the user is revoked.
     * @throws RepositoryException
     * @throws UserAccountNotFoundException
     */
    public function __invoke(AuthenticateUserCommand $command): Entity
    {
        $account = $this->userAccountRepo->get(new EmailAddress($command->email));
        /** @noinspection PhpUndefinedMethodInspection */
        if (!$account->login($command->password)) {
            throw new AuthenticationException('Invalid password');
        }
        /** @noinspection PhpUndefinedMethodInspection */
        if ($account->isRevoked()) {
            throw new AuthenticationException('User is revoked');
        }

        $this->userAccountRepo->update($account);

        $accessToken = $this->accessTokenRepo->create(
            new AccessToken(
                identifier: new TokenIdentifier(),
                expiration: Timestamp::createFromDateTime(
                    new DateTime('now +2 hours')
                ),
                account: $account
            )
        );

        return $this->refreshTokenRepo->create(
            new RefreshToken(
                identifier: new TokenIdentifier(),
                expiration: Timestamp::createFromDateTime(
                    new DateTime('now +1 month')
                ),
                accessToken: $accessToken
            )
        );
    }
}

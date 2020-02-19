<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Timestamp;

use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;

use Firebase\JWT\JWT;

/**
 * Usecase: Authenticate a user and create a refresh token.
 */
final class AuthenticateUser
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepo;

    /**
     * @var RefreshTokenRepository
     */
    private $refreshTokenRepo;

    /**
     * Constructor.
     * @param UserRepository $userRepo A user repository
     * @param AccessTokenRepository $accessTokenRepo An accesstoken repository
     * @param RefreshTokenRepository $refreshTokenRepo An refreshtoken repository
     */
    public function __construct(
        UserRepository $userRepo,
        AccessTokenRepository $accessTokenRepo,
        RefreshTokenRepository $refreshTokenRepo
    ) {
        $this->userRepo = $userRepo;
        $this->accessTokenRepo = $accessTokenRepo;
        $this->refreshTokenRepo = $refreshTokenRepo;
    }

    /**
     * Find the user by email and check the password. When the password is
     * not verified or the user is revoked an AuthenticationException will
     * be thrown. On success, a refreshtoken will be returned. This refresh
     * token will contain an access token. This access token will be
     * associated with the authenticated user.
     * @param  AuthenticateUserCommand $command
     * @return Entity<RefreshToken>              A RefreshToken entity
     * @throws \Kwai\Core\Domain\Exceptions\NotFoundException
     *    Thrown when user can't be found
     * @throws AuthenticationException
     *    Thrown when the password is invalid, or when the user is revoked.
     */
    public function __invoke(AuthenticateUserCommand $command): Entity
    {
        $account = $this->userRepo->getAccount(new EmailAddress($command->email));
        if (!$account->login($command->password)) {
            throw new AuthenticationException('Invalid password');
        }
        if ($account->isRevoked()) {
            throw new AuthenticationException('User is revoked');
        }

        $accessToken = $this->accessTokenRepo->create(
            new AccessToken((object) [
                'identifier' => new TokenIdentifier(),
                'expiration' => Timestamp::createFromDateTime(
                    new \DateTime('now +2 hours')
                ),
                'account' => $account
            ])
        );

        $refreshToken = $this->refreshTokenRepo->create(
            new RefreshToken((object)[
                'identifier' => new TokenIdentifier(),
                'expiration' => Timestamp::createFromDateTime(
                    new \DateTime('now +1 month')
                ),
                'accessToken' => $accessToken
            ])
        );

        return $refreshToken;
    }
}

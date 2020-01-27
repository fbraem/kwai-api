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
use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;

use Firebase\JWT\JWT;

/**
 * Usecase: Authenticate a user
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
     * Constructor.
     * @param UserRepository $userRepo A user repository
     * @param AccessTokenRepository $accessTokenRepo An accesstoken repository
     */
    public function __construct(
        UserRepository $userRepo,
        AccessTokenRepository $accessTokenRepo
    ) {
        $this->userRepo = $userRepo;
        $this->accessTokenRepo = $accessTokenRepo;
    }

    /**
     * Find the user by email and check the password. When the password is
     * not verified or the user is revoked an AuthenticationException will
     * be thrown. On success, the user and accesstoken will be returned.
     * @param  AuthenticateUserCommand $command
     * @return Entity<AccessToken>              An AccessToken entity
     * @throws \Kwai\Core\Domain\Exceptions\NotFoundException
     *    Thrown when user can't be found
     * @throws AuthenticationException
     *    Thrown when the password is invalid, or when the user is revoked.
     */
    public function __invoke(AuthenticateUserCommand $command): Entity
    {
        $user = $this->userRepo->getByEmail(new EmailAddress($command->email));
        if (!$user->login($command->password)) {
            throw new AuthenticationException('Invalid password');
        }
        if ($user->isRevoked()) {
            throw new AuthenticationException('User is revoked');
        }

        $future = new \DateTime('now +2 hours');

        $accessToken = new AccessToken((object) [
            'identifier' => new TokenIdentifier(),
            'expiration' => Timestamp::createFromDateTime($future)
        ]);
        $accessToken->attachUser($user);
        return $this->accessTokenRepo->create($accessToken);
    }
}

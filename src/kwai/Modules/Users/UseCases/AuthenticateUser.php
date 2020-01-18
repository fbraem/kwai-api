<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;

use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Core\Domain\EmailAddress;

/**
 * Usecase: Authenticate a user
 */
final class AuthenticateUser
{
    private $userRepo;

    /**
     * Constructor.
     * @param UserRepository $userRepo A user repository
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Find the user by email and check the password.
     * @param  AuthenticateUserCommand $command
     * @return Entity                           The user if authentication is
     *                                          successful.
     * @throws NotFoundExcpetion                Thrown when user can't be found.
     */
    public function __invoke(AuthenticateUserCommand $command): Entity
    {
        $user = $this->userRepo->getByEmail(new EmailAddress($command->email));
        if ($user->login($command->password)) {
            return $user;
        }
    }
}

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

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function __invoke(AuthenticateUserCommand $command): Entity
    {
        try {
            $user = $this->userRepo->getByEmail(new EmailAddress($command->email));
            if ($user->login($command->password)) {
                return $user;
            }
        } catch (NotFoundException $nfe) {
        }
    }
}

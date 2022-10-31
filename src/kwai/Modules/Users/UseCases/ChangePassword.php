<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Repositories\UserAccountRepository;

/**
 * Class ResetPassword
 *
 * Change the password of the active user.
 */
class ChangePassword
{
    public function __construct(
        private readonly UserAccountRepository $userAccountRepo
    ) {
    }

    public static function create(
        UserAccountRepository $userAccountRepo
    ): ChangePassword {
        return new self($userAccountRepo);
    }

    /**
     * @param ChangePasswordCommand $command
     * @param UserEntity $user
     * @throws RepositoryException
     * @throws UserAccountNotFoundException
     * @throws NotAllowedException
     */
    public function __invoke(ChangePasswordCommand $command, UserEntity $user): void
    {
        $userAccount = $this->userAccountRepo->get($user->getEmailAddress());
        $userAccount->resetPassword(Password::fromString($command->password));
        $userAccount->getUser()->getTraceableTime()->markUpdated();
        $this->userAccountRepo->update($userAccount);
    }
}
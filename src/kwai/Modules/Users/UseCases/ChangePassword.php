<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\UserAccountEntity;
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
     * @param UserAccountEntity $userAccount
     * @throws NotAllowedException
     * @throws RepositoryException
     */
    public function __invoke(ChangePasswordCommand $command, UserAccountEntity $userAccount): void
    {
        $userAccount->resetPassword(Password::fromString($command->password));
        $userAccount->getUser()->getTraceableTime()->markUpdated();
        $this->userAccountRepo->update($userAccount);
    }
}
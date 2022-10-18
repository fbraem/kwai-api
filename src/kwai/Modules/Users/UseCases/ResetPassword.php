<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\Exceptions\UserRecoveryExpiredException;
use Kwai\Modules\Users\Domain\Exceptions\UserRecoveryNotFoundException;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use Kwai\Modules\Users\Repositories\UserRecoveryRepository;

/**
 * Class ResetPassword
 *
 * Resets the password of a user.
 */
class ResetPassword
{
    public function __construct(
        private readonly UserRecoveryRepository $userRecoveryRepo,
        private readonly UserAccountRepository $userAccountRepo
    ) {
    }

    public static function create(
        UserRecoveryRepository $userRecoveryRepo,
        UserAccountRepository $userAccountRepo
    ): ResetPassword {
        return new self($userRecoveryRepo, $userAccountRepo);
    }

    /**
     * @param ResetPasswordCommand $command
     * @throws UserRecoveryExpiredException
     * @throws RepositoryException
     * @throws UserAccountNotFoundException
     * @throws UserRecoveryNotFoundException
     * @throws NotAllowedException
     */
    public function __invoke(ResetPasswordCommand $command): void
    {
        $uuid = new UniqueId($command->uuid);
        $recovery = $this->userRecoveryRepo->getByUniqueId($uuid);

        if ($recovery->isExpired()) {
            throw new UserRecoveryExpiredException($uuid);
        }

        $userAccount = $this->userAccountRepo->get($recovery->getUser()->getEmailAddress());
        $userAccount->resetPassword(Password::fromString($command->password));
        $userAccount->getUser()->getTraceableTime()->markUpdated();
        $this->userAccountRepo->update($userAccount);
    }
}
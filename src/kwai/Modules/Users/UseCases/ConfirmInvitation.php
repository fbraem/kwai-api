<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Usecase: confirm an invitation and create a new user account
 * - Step 1 - Check if the invitation exists and that it is not expired
 * - Step 2 - Create the user
 */
final class ConfirmInvitation
{
    /**
     * ConfirmInvitation constructor.
     *
     * @param UserInvitationRepository $invitationRepo
     * @param UserAccountRepository    $userAccountRepository
     */
    public function __construct(
        private readonly UserInvitationRepository $invitationRepo,
        private readonly UserAccountRepository $userAccountRepository
    ) {
    }

    /**
     * Factory method
     *
     * @param UserInvitationRepository $invitationRepository
     * @param UserAccountRepository    $userAccountRepository
     * @return ConfirmInvitation
     */
    public static function create(
        UserInvitationRepository $invitationRepository,
        UserAccountRepository $userAccountRepository
    ): self
    {
        return new self($invitationRepository, $userAccountRepository);
    }

    /**
     * Create a new user account.
     *
     * @param ConfirmInvitationCommand $command
     * @return UserAccountEntity The new user account
     * @throws RepositoryException
     * @throws UnprocessableException
     * @throws UserInvitationNotFoundException
     */
    public function __invoke(ConfirmInvitationCommand $command): UserAccountEntity
    {
        $invitation = $this->invitationRepo->getByUniqueId(new UniqueId($command->uuid));
        if ($invitation->isExpired()) {
            throw new UnprocessableException(('User invitation is expired'));
        }

        if ($invitation->isRevoked()) {
            throw new UnprocessableException('User invitation is revoked');
        }

        if ($invitation->isConfirmed()) {
            throw new UnprocessableException('User invitation is already confirmed');
        }

        $email = $invitation->getEmailAddress();
        try {
            $this->userAccountRepository->get($email);
            throw new UnprocessableException('Email is already used');
        } catch (UserAccountNotFoundException) {
        }

        $user = new User(
            uuid: new UniqueId(),
            emailAddress: $email,
            username: new Name(
                $command->firstName,
                $command->lastName
            )
        );

        $account = new UserAccount(
            user: $user,
            password: Password::fromString($command->password)
        );

        $invitation->confirm();
        $this->invitationRepo->update($invitation);

        return $this->userAccountRepository->create($account);
    }
}

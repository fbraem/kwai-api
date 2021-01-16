<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Usecase: confirm an invitation and create a new user account
 * - Step 1 - Check if the invitation exists and that it is not expired
 * - Step 2 - Create the user
 */
final class ConfirmInvitation
{
    /**
     * ConfirmInvitation constructor.
     * @param UserInvitationRepository $invitationRepo
     * @param UserRepository $userRepo
     */
    public function __construct(
        private UserInvitationRepository $invitationRepo,
        private UserRepository $userRepo
    ) {
    }

    /**
     * Factory method
     *
     * @param UserInvitationRepository $invitationRepository
     * @param UserRepository           $userRepository
     * @return ConfirmInvitation
     */
    public static function create(
        UserInvitationRepository $invitationRepository,
        UserRepository $userRepository
    ) {
        return new self($invitationRepository, $userRepository);
    }

    /**
     * Create a new user account.
     * @param ConfirmInvitationCommand $command
     * @return Entity The new user account
     * @throws NotFoundException
     * @throws UnprocessableException
     * @throws RepositoryException
     * @noinspection PhpUndefinedMethodInspection
     */
    public function __invoke(ConfirmInvitationCommand $command): Entity
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

        $email = new EmailAddress($command->email);
        if ($this->userRepo->existsWithEmail($email)) {
            throw new UnprocessableException('Email is already used');
        }

        $user = new User((object)[
            'uuid' => new UniqueId(),
            'emailAddress' => $email,
            'username' => new Name($command->firstName, $command->lastName)
        ]);

        $account = new UserAccount((object) [
            'user' => $user,
            'password' => Password::fromString($command->password)
        ]);

        $invitation->confirm();
        $this->invitationRepo->update($invitation);

        return $this->userRepo->create($account);
    }
}

<?php
/**
 * Testcase for GetCurrentUser
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\PlatesEngine;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Mails\Infrastructure\Repositories\RecipientDatabaseRepository;
use Kwai\Modules\Mails\Repositories\MailRepository;
use Kwai\Modules\Mails\Repositories\RecipientRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;
use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\UseCases\InviteUser;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class InviteUserTest extends DatabaseTestCase
{
    private UserInvitationRepository $invitationRepo;

    private UserRepository $userRepo;

    private MailRepository $mailRepo;

    private RecipientRepository $recipientRepo;

    private Entity $user;

    public function setup(): void
    {
        $this->invitationRepo = new UserInvitationDatabaseRepository(self::$db);
        $this->userRepo = new UserDatabaseRepository(self::$db);
        $this->mailRepo = new MailDatabaseRepository(self::$db);
        $this->recipientRepo = new RecipientDatabaseRepository(self::$db);
        try {
            $this->user = $this->userRepo->getByEmail(new EmailAddress('test@kwai.com'));
        } catch (NotFoundException $e) {
            self::assertTrue(false, $e->getMessage());
        } catch (RepositoryException $e) {
            self::assertTrue(false, $e->getMessage());
        }
    }

    public function testInviteUser(): void
    {
        $command = new InviteUserCommand();
        $command->sender_mail = 'test@kwai.com';
        $command->sender_name = 'Webmaster';
        $command->email = 'jigoro.kano@kwai.com';
        $command->name = 'Jigoro Kano';
        $command->expiration = 14;

        $engine = new PlatesEngine(__DIR__);
        $template = new MailTemplate(
            'User invitation kwai@com',
            $engine->createTemplate('html_template'),
            $engine->createTemplate('template')
        );

        try {
            $invitation = (new InviteUser(
                $this->invitationRepo,
                $this->userRepo,
                $this->mailRepo,
                $this->recipientRepo,
                $template,
                $this->user
            ))($command);
            $this->assertInstanceOf(
                Entity::class,
                $invitation
            );
        } catch (UnprocessableException $e) {
            self::assertTrue(false, $e->getMessage());
        } catch (RepositoryException $e) {
            self::assertTrue(false, $e->getMessage());
        }
    }
}

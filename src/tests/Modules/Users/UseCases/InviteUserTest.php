<?php
/**
 * Testcase for GetCurrentUser
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\AlreadyExistException;
use Kwai\Core\Domain\Exceptions\NotFoundException;
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
            echo $e->getMessage(), PHP_EOL;
        }
    }

    public function testInviteUser(): void
    {
        $command = new InviteUserCommand([
            'sender_mail' => 'test@kwai.com',
            'sender_name' => 'Webmaster',
            'email' => 'jigoro.kano@kwai.com',
            'name' => 'Jigoro Kano',
            'expiration' => 14,
        ]);

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
        } catch (AlreadyExistException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}

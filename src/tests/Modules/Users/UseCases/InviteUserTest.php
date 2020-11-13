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
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\PlatesEngine;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Mails\Infrastructure\Repositories\RecipientDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\InviteUser;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
use Tests\Context;

$context = Context::createContext();

it('can invite a user', function () use ($context) {
    $invitationRepo = new UserInvitationDatabaseRepository($context->db);
    $userRepo = new UserDatabaseRepository($context->db);
    $mailRepo = new MailDatabaseRepository($context->db);
    $recipientRepo = new RecipientDatabaseRepository($context->db);
    try {
        $user = $userRepo->getByEmail(
            new EmailAddress('test@kwai.com')
        );
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
    if (!isset($user)) {
        return;
    }

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
            $invitationRepo,
            $userRepo,
            $mailRepo,
            $recipientRepo,
            $template,
            $user
        ))($command);
        $this->assertInstanceOf(
            Entity::class,
            $invitation
        );
    } catch (UnprocessableException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

<?php
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\PlatesEngine;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\InviteUser;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
use Tests\Context;

$context = Context::createContext();

it('can invite a user', function () use ($context) {
    $invitationRepo = new UserInvitationDatabaseRepository($context->db);
    $mailRepo = new MailDatabaseRepository($context->db);
    $accountRepo = new UserAccountDatabaseRepository($context->db);

    $command = new InviteUserCommand();
    $command->sender_mail = 'test@kwai.com';
    $command->sender_name = 'Webmaster';
    $command->email = 'ingrid.berghmans' . rand(1, 100) . '@kwai.com';
    $command->name = 'Ingrid Berghmans';
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
            $accountRepo,
            $mailRepo,
            $template,
            new Creator(1, new Name('Jigoro', 'Kano'))
        ))($command);
        expect($invitation)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (UnprocessableException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

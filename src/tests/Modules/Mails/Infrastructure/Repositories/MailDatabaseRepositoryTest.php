<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\Mails\Infrastructure\Repositories;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can create an email', function () use ($context) {
    $user = null;
    $userRepo = new UserDatabaseRepository($context->db);
    try {
        $user = $userRepo->getByEmail(new EmailAddress('test@kwai.com'));
    } catch (NotFoundException $e) {
        $this->assertTrue(false, strval($e));
    } catch (DatabaseException $e) {
        $this->assertTrue(false, strval($e));
    } catch (RepositoryException $e) {
        $this->assertTrue(false, strval($e));
    }

    $repo = new MailDatabaseRepository($context->db);
    try {
        $mail = $repo->create(new Mail(
            (object)[
                'tag' => 'test',
                'uuid' => new UniqueId(),
                'sender' => new Address(
                    new EmailAddress('test@kwai.com')
                ),
                'content' => new MailContent('Test', 'This mail is a test'),
                'traceableTime' => new TraceableTime(),
                'creator' => $user,
                'recipients' => [
                    new Recipient((object)[
                        'type' => RecipientType::TO(),
                        'address' => new Address(
                            new EmailAddress('jigoro.kano@kwai.com'),
                            'Jigoro Kano'
                        )
                    ])
                ]
            ]
        ));
        expect($mail)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($mail->domain())
            ->toBeInstanceOf(Mail::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        expect($mail->getTag())->toBe('test');
    } catch (RepositoryException $e) {
        $this->assertTrue(false, strval($e));
    }
});

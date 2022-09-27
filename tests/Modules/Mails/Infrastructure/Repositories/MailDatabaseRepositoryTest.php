<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\Mails\Infrastructure\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);

beforeEach(fn() => $this->withDatabase());

it('can create an email', function () {
    $creator = new Creator(
        id: 1,
        name: new Name('Jigoro', 'Kano')
    );
    $repo = new MailDatabaseRepository($this->db);
    try {
        $mail = $repo->create(new Mail(
            uuid: new UniqueId(),
            sender: new Address(
                new EmailAddress('test@kwai.com')
            ),
            content: new MailContent('Test', 'This mail is a test'),
            creator: $creator,
            traceableTime: new TraceableTime(),
            tag: 'test',
            recipients: collect([
                new Recipient(
                    type: RecipientType::TO,
                    address: new Address(
                        email: new EmailAddress('jigoro.kano@kwai.com'),
                        name: 'Jigoro Kano'
                    )
                )
            ])
        ));
        expect($mail)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($mail->domain())
            ->toBeInstanceOf(Mail::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        expect($mail->getTag())->toBe('test');
        return $mail->id();
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available');
;

it('can get all mails', function () {
    $repo = new MailDatabaseRepository($this->db);
    try {
        $mails = $repo->getAll();
        expect($mails)
            ->toBeInstanceOf(Collection::class)
            ->and($mails->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available');
;

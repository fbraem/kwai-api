<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\Mails\Infrastructure\Repositories;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Mails\Repositories\MailRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Tests\DatabaseTestCase;

class MailDatabaseRepositoryTest extends DatabaseTestCase
{
    private MailRepository $repo;

    public function setup(): void
    {
        $this->repo = new MailDatabaseRepository(self::$db);
    }

    /**
     * @return Entity<Mail>
     */
    public function testCreate(): Entity
    {
        $user = null;
        $userRepo = new UserDatabaseRepository(self::$db);
        try {
            $user = $userRepo->getByEmail(new EmailAddress('test@kwai.com'));
        } catch (NotFoundException $e) {
            $this->assertTrue(false, strval($e));
        } catch (DatabaseException $e) {
            $this->assertTrue(false, strval($e));
        }

        try {
            $mail = $this->repo->create(new Mail(
                (object) [
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
            $this->assertInstanceOf(Entity::class, $mail);
            return $mail;
        } catch (DatabaseException $e) {
            $this->assertTrue(false, strval($e));
        }
        return null;
    }

    /**
     * @depends testCreate
     */
    public function testGetByUUID()
    {
    }

    /**
     * @depends testCreate
     */
    public function testGetById()
    {
    }
}

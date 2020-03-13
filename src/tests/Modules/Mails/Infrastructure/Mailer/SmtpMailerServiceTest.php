<?php
/**
 * Testcase for SmtpMailerService
 */
declare(strict_types=1);

namespace Tests\Modules\Mails\Infstrastucture\Mailer;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerServiceFactory;
use PHPUnit\Framework\TestCase;

use Kwai\Modules\Mails\Infrastructure\Mailer\SimpleMessage;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerException;

/**
 * @group mail
 */
final class SmtpMailerServiceTest extends TestCase
{
    public function testSimpleMail()
    {
        $mailer = (new MailerServiceFactory())->create(
            $_ENV['smtp']
        );

        $result = $mailer->send(
            new SimpleMessage('Hello', 'World'),
            new Address(new EmailAddress($_ENV['from'])),
            ['franky.braem@gmail.com' => 'Franky Braem']
        );
        $this->assertGreaterThan(0, $result, 'No mails send');
    }

    public function testSimpleNoRecipients()
    {
        $this->expectException(MailerException::class);

        $mailer = (new MailerServiceFactory())->create(
            $_ENV['smtp']
        );

        $mailer->send(
            new SimpleMessage('Hello', 'World'),
            new Address(new EmailAddress($_ENV['from'])),
            []
        );
    }
}

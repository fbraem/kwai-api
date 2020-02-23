<?php
/**
 * Testcase for SmtpMailerService
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Modules\Mails\Infrastructure\Mailer\SmtpMailerService;
use Kwai\Modules\Mails\Infrastructure\Mailer\SimpleMessage;
use Kwai\Modules\Mails\Infrastructure\Mailer\Recipient;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerException;

/**
 * @group mail
 */
final class SmtpMailerServiceTest extends TestCase
{
    public function testSimpleMail()
    {
        $mailer = new SmtpMailerService(
            $_ENV['smtp'],
            $_ENV['from']
        );

        try {
            $mailer->send(
                new SimpleMessage('Hello', 'World'),
                ['franky.braem@gmail.com' => 'Franky Braem']
            );
            $mailSend = true;
        } catch (MailerException $me) {
            $mailSend = false;
        }
        $this->assertTrue($mailSend);
    }

    public function testSimpleInvalidMailHost()
    {
        $this->expectException(MailerException::class);

        $mailer = new SmtpMailerService(
            'smtp://wrong:user@smtp.mailtrap.io',
            $_ENV['from']
        );

        $mailer->send(
            new SimpleMessage('Hello', 'World'),
            ['franky.braem@gmail.com' => 'Franky Braem']
        );
    }

    public function testSimpleNoRecpients()
    {
        $this->expectException(MailerException::class);

        $mailer = new SmtpMailerService(
            $_ENV['smtp'],
            $_ENV['from']
        );
        $mailer->send(
            new SimpleMessage('Hello', 'World'),
            []
        );
    }
}

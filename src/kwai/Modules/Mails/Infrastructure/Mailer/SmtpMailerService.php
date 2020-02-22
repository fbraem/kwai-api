<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Genkgo\Mail\Transport\SmtpTransport;
use Genkgo\Mail\Transport\InjectStandardHeadersTransport;
use Genkgo\Mail\Transport\EnvelopeFactory;
use Genkgo\Mail\Protocol\Smtp\ClientFactory;
use Genkgo\Mail\Header\From;
use Genkgo\Mail\Header\To;
use Genkgo\Mail\Header\Subject;
use Genkgo\Mail\AddressList;
use Genkgo\Mail\Address;
use Genkgo\Mail\EmailAddress;
use Genkgo\Mail\Exception\AssertionFailedException;

/**
 * Mailer service for sending mails using SMTP
 */
final class SmtpMailerService implements MailerService
{
    /**
     * The mailer
     * @var SmtpTransport
     */
    private $transport;

    /**
     * The sender
     * @var From
     */
    private $from;

    /**
     * Constructor
     * @param string $connection SMTP connection string
     * @param array|string $from Emailaddress or EmailAddress and name.
     */
    public function __construct(
        string $connection,
        $from
    ) {
        $this->transport = new SmtpTransport(
            ClientFactory::fromString($connection)->newClient(),
            EnvelopeFactory::useExtractedHeader()
        );
        if (is_array($from)) {
            $this->from = From::fromAddress($from[0], $from[1]);
        } else {
            $this->from = From::fromEmailAddress($from);
        }
    }

    /**
     * @inheritdoc
     * @throws MailerException
     */
    public function send(
        Message $message,
        array $to,
        array $cc = [],
        array $bcc = []
    ): void {
        if (count($to) == 0) {
            throw new MailerException('No recipients');
        }

        // This method will be used with array_map to create an Address instance
        // of each element. When the element has an integer key
        // there is no name passed, otherwise the key is the email and the
        // value the name. This makes it possible to pass such arrays:
        // var $to = [
        //  'test@example.com',
        //  'test2@example.com' => 'test2'
        // ];
        $convertArrayToAddress = function ($key, $value) {
            if (is_int($key)) {
                return new Address(new EmailAddress($value));
            }
            return new Address(
                new EmailAddress($key),
                $value
            );
        };

        try {
            $this->transport->send(
                $message->createMessage()
                    ->withHeader($this->from)
                    ->withHeader(new Subject($message->getSubject()))
                    ->withHeader(new To(new AddressList(
                        array_map(
                            $convertArrayToAddress,
                            array_keys($to),
                            $to
                        )
                    )))
            );
        } catch (AssertionFailedException $afe) {
            throw new MailerException('Unable to send mail', $afe);
        }
    }
}

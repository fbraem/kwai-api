<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Swift_SmtpTransport;
use Swift_TransportException;

/**
 * Mailer service for sending mails using SMTP
 */
final class SmtpMailerService implements MailerService
{
    /**
     * The mailer
     * @var Swift_SmtpTransport
     */
    private Swift_SmtpTransport $transport;

    /**
     * Constructor
     * @param string $user
     * @param string $pwd
     * @param string $host
     * @param int $port
     */
    public function __construct(
        string $user,
        string $pwd,
        string $host,
        int $port = 25
    ) {
        $this->transport = (new Swift_SmtpTransport(
            $host,
            $port
        ))
            ->setUsername($user)
            ->setPassword($pwd)
        ;
    }

    /**
     * @inheritdoc
     * @throws MailerException
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function send(
        Message $message,
        Address $from,
        array $to,
        array $cc = [],
        array $bcc = []
    ): int {
        if (count($to) == 0) {
            throw new MailerException('No recipients');
        }

        $message = $message->createMessage()
            ->setFrom([strval($from->getEmail()) => $from->getName()])
            ->setTo($to)
            ->setCc($cc)
            ->setBcc($bcc)
        ;
        try {
            return $this->transport->send($message);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (Swift_TransportException $e) {
            var_dump($e);
            throw new MailerException('Unable to send mail', $e);
        }
    }
}

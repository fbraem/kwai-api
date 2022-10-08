<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Configuration\MailerConfiguration;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/**
 * Mailer service for sending mails using SMTP
 */
final class SymfonyMailerService implements MailerService
{
    private Mailer $mailer;

    /**
     * Constructor
     */
    public function __construct(
        private readonly MailerConfiguration $config
    ) {
        $this->mailer = new Mailer(Transport::fromDsn((string) $config->getDsn()));
    }

    /**
     * @inheritdoc
     * @throws MailerException
     */
    public function send(Message $message, array $recipients = [], ?Address $from = null): void {
        $from ??= $this->config->getFromAddress();
        if ($from == null) {
            throw new MailerException('No sender');
        }

        $to = array_map(
            fn(Recipient $recipient) => $recipient->getAddress(),
            array_filter(
                $recipients,
                fn(Recipient $recipient) => $recipient->getType() === RecipientType::TO
            )
        );
        if (count($to) == 0) {
            throw new MailerException('No recipients');
        }

        $mapToSymfonyAddress = static fn($address) => new \Symfony\Component\Mime\Address(
            (string) $address->getEmail(), $address->getName()
        );

        $email = $message->processMail(new Email());
        $email = $email->from(new \Symfony\Component\Mime\Address(
                (string) $from->getEmail(),
                $from->getName()
            ))
            ->to(...array_map($mapToSymfonyAddress, $to))
        ;
        $cc = array_map(
            fn(Recipient $recipient) => $recipient->getAddress(),
            array_filter(
                $recipients,
                fn(Recipient $recipient) => $recipient->getType() === RecipientType::CC
            )
        );
        if (count($cc) > 0) {
            $email = $email->cc(...array_map($mapToSymfonyAddress, $cc));
        }
        $bcc = array_map(
            fn(Recipient $recipient) => $recipient->getAddress(),
            array_filter(
                $recipients,
                fn(Recipient $recipient) => $recipient->getType() === RecipientType::BCC
            )
        );
        if (count($bcc) > 0) {
            $email = $email->bcc(...array_map($mapToSymfonyAddress, $bcc));
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new MailerException('Could not send email', $e);
        }
    }
}

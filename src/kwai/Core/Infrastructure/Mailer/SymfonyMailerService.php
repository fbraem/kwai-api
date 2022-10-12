<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Configuration\MailerConfiguration;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
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
    public function send(Message $message): void {
        if (count($message->getRecipients()->getTo()) == 0) {
            throw new MailerException('No recipients');
        }

        $mapToSymfonyAddress = static fn(Identity $address) => new Address(
            (string) $address->getEmail(), $address->getName()
        );

        $email = (new Email())
            ->from(new Address(
                $message->getRecipients()->getFrom()->getEmail(),
                $message->getRecipients()->getFrom()->getName()
            ))
            ->subject($message->getSubject())
            ->to(...array_map($mapToSymfonyAddress, $message->getRecipients()->getTo()))
        ;
        if (count($message->getRecipients()->getCc()) > 0) {
            $email = $email->cc(...array_map($mapToSymfonyAddress, $message->getRecipients()->getCc()));
        }
        if (count($message->getRecipients()->getBcc()) > 0) {
            $email = $email->bcc(...array_map($mapToSymfonyAddress, $message->getRecipients()->getBcc()));
        }

        $text = $message->getText();
        if ($text)
            $email = $email->text($text);

        $html = $message->getText();
        if ($html)
            $email = $email->html($html);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new MailerException('Could not send email', $e);
        }
    }

    public function createRecipients(): Recipients
    {
        $from = $this->config->getFromAddress();
        return new Recipients(
            new Recipient((string) $from->getEmail(), $from->getName())
        );
    }
}

<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Configuration\MailerConfiguration;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

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
        private MailerConfiguration $config
    ) {
        $this->mailer = new Mailer(Transport::fromDsn((string) $config->getDsn()));
    }

    /**
     * @inheritdoc
     */
    public function getFrom(): Address
    {
        return $this->config->getFromAddress();
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

        $mapToSymfonyAddress = static fn($address) => new \Symfony\Component\Mime\Address(
            (string) $address->getEmail(), $address->getName()
        );

        $from = $message->getFrom() ?? $this->getFrom();
        $symfonyMessage = $message->createMessage()
            ->from(new \Symfony\Component\Mime\Address(
                (string) $from->getEmail(),
                $from->getName()
            ))
            ->to(...array_map($mapToSymfonyAddress, $to))
        ;
        if (count($cc) > 0) {
            $symfonyMessage = $symfonyMessage->cc(...array_map($mapToSymfonyAddress, $cc));
        }
        if (count($bcc) > 0) {
            $symfonyMessage = $symfonyMessage->bcc(...array_map($mapToSymfonyAddress, $bcc));
        }

        try {
            $this->mailer->send($symfonyMessage);
        } catch (TransportExceptionInterface $e) {
            throw new MailerException('Could not send email', $e);
        }
    }
}

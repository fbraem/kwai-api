<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;

use Kwai\Core\Infrastructure\Mappers\CreatorMapper;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

/**
 * Mapper for Mail entity
 */
final class MailMapper
{
    /**
     * Creates a Mail domain object from a database row.
     *
     * @param Collection $data
     * @return Mail
     */
    public static function toDomain(Collection $data): Mail
    {
        return new Mail(
            uuid: new UniqueId($data->get('raw')),
            sender: new Address(new EmailAddress($data->get('sender_email')), $data->get('sender_name', '')),
            content: new MailContent($data->get('subject'), $data->get('text_body'), $data->get('html_body', '')),
            creator: CreatorMapper::toDomain($data->get('user')),
            sentTime: $data->has('sent_time') ? Timestamp::createFromString($data->get('sent_time')) : null,
            remark: $data->get('remark'),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                ? Timestamp::createFromString($data->get('updated_at'))
                : null
            ),
            tag: $data->get('tag'),
            recipients: $data->get('recipients')->map(
                fn ($recipient) => RecipientMapper::toDomain($recipient)
            )
        );
    }

    /**
     * Returns an array representation of Mail to store it in a database.
     *
     * @param Mail $mail
     * @return Collection
     */
    public static function toPersistence(Mail $mail): Collection
    {
        return collect([
            'uuid' => strval($mail->getUniqueId()),
            'tag' => $mail->getTag(),
            'sender_email' => $mail->getSender()->getEmail(),
            'sender_name' => $mail->getSender()->getName(),
            'subject' => $mail->getContent()->getSubject(),
            'html_body' => $mail->getContent()->getHtml(),
            'text_body' => $mail->getContent()->getText(),
            'sent_time' => $mail->getSentTime()?->__toString(),
            'remark' => $mail->getRemark(),
            'user_id' => $mail->getCreator()->getId(),
            'created_at' => strval($mail->getTraceableTime()->getCreatedAt()),
            'updated_at' => $mail->getTraceableTime()->getUpdatedAt()?->__toString(),
        ]);
    }
}

<?php
/**
 * @package kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mappers;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;

/**
 * Mapper for Mail entity
 */
final class MailMapper
{
    /**
     * Creates a Mail entity from a database row.
     * @param object $raw
     * @return Entity<Mail>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Mail((object)[
                'tag' => $raw->tag ?? null,
                'uuid' => new UniqueId($raw->uuid),
                'sender' => new Address($raw->sender_email, $raw->sender_name ?? ''),
                'content' => new MailContent($raw->subject, $raw->text_body, $raw->html_body ?? ''),
                'sent_time' => $raw->sent_time ? Timestamp::createFromString($raw->sent_time) : null,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at) ? Timestamp::createFromString($raw->updated_at) : null
                ),
                'remark' => $raw->remark ?? null,
                'creator' => UserMapper::toDomain($raw->user),
                'recipients' => array_map(
                    fn($recipient) => RecipientMapper::toDomain($recipient),
                    $raw->recipients
                )
            ])
        );
    }

    /**
     * Returns an array representation of Mail to store it in a database.
     * @param Mail $mail
     * @return array
     */
    public static function toPersistence(Mail $mail): array
    {
        if ($mail->getTraceableTime()->getUpdatedAt()) {
            $updated_at = strval(
                $mail->getTraceableTime()->getUpdatedAt()
            );
        } else {
            $updated_at = null;
        }
        return [
            'uuid' => strval($mail->getUniqueId()),
            'tag' => $mail->getTag(),
            'sender_email' => $mail->getSender()->getEmail(),
            'sender_name' => $mail->getSender()->getName(),
            'subject' => $mail->getContent()->getSubject(),
            'html_body' => $mail->getContent()->getHtml(),
            'text_body' => $mail->getContent()->getText(),
            'sent_time' => $mail->getSentTime() ? strval($mail->getSentTime()) : null,
            'remark' => $mail->getRemark(),
            'user_id' => $mail->getCreator()->id(),
            'created_at' => strval($mail->getTraceableTime()->getCreatedAt()),
            'updated_at' => $updated_at
        ];
    }
}

<?php
/**
 * @package kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;

use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;

/**
 * Mapper for Recipient entity
 */
final class RecipientMapper
{
    /**
     * Creates a Recipient entity from a database row.
     * @param object $raw
     * @return Entity<Recipient>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Recipient((object)[
                'type' => new RecipientType($raw->type),
                'address' => new Address($raw->email, $raw->sender_name ?? '')
            ])
        );
    }

    /**
     * Returns an array representation of recipient to store it in a database.
     * @param Recipient $recipient
     * @return array
     */
    public static function toPersistence(Recipient $recipient): array
    {
        return [
            'type' => strval($recipient->getType()),
            'email' => $recipient->getAddress()->getEmail(),
            'name' => $recipient->getAddress()->getName()
        ];
    }
}

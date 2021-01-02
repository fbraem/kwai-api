<?php
/**
 * @package kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
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
     *
     * @param Collection $data
     * @return Recipient
     */
    public static function toDomain(Collection $data): Recipient
    {
        return new Recipient(
            type: new RecipientType((int) $data->get('type')),
            address: new Address(
                new EmailAddress($data->get('email')),
                $data->get('name', '')
            )
        );
    }

    /**
     * Returns a Collection representation of recipient to store it in a database.
     *
     * @param Recipient $recipient
     * @return Collection
     */
    public static function toPersistence(Recipient $recipient): Collection
    {
        return collect([
            'type' => strval($recipient->getType()),
            'email' => $recipient->getAddress()->getEmail(),
            'name' => $recipient->getAddress()->getName()
        ]);
    }
}

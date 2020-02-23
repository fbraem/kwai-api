<?php
/**
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mappers;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\EmailAddress;
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
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Mail((object)[
                'uuid' => new UniqueId($raw->uuid),
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at) ? Timestamp::createFromString($raw->updated_at) : null
                ),
                'creator' => UserMapper::toDomain($raw->user),
                'recipients' => []
            ])
        );
    }

    public static function toPersistence(Mail $mail): array
    {
        return [];
        /*
                if ($accessToken->getTraceableTime()->getUpdatedAt()) {
                    $updated_at = strval(
                        $accessToken->getTraceableTime()->getUpdatedAt()
                    );
                } else {
                    $updated_at = null;
                }
                return [
                    'identifier' => strval($accessToken->getIdentifier()),
                    'expiration' => strval($accessToken->getExpiration()),
                    'revoked' => $accessToken->isRevoked() ? '1' : '0',
                    'created_at' => strval($accessToken->getTraceableTime()->getCreatedAt()),
                    'updated_at' => $updated_at,
                    'user_id' => $accessToken->getUserAccount()->id()
                ];
        */
    }
}

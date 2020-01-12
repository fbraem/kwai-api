<?php
/**
 * User Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\DateTime;

use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

final class AccessTokenMapper
{
    public static function toDomain(object $raw): AccessToken
    {
        return AccessToken::create((object)[
            'identifier' => new TokenIdentifier($raw->identifier),
            'expiration' => DateTime::createFromString($raw->expiration),
            'revoked' => $raw->revoked,
            'traceableTime' => new TraceableTime(
                DateTime::createFromString($raw->created_at),
                isset($raw->updated_at) ? DateTime::createFromString($raw->updated_at) : null
            ),
        ], $raw->id);
    }

    public static function toPersistence(AccessToken $accessToken): object
    {
    }
}

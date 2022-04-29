<?php
declare(strict_types=1);

namespace Tests;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;

/**
 * Class Context
 *
 * Use this class to create a context for tests. When a database is available
 * @deprecated (Start using DatabaseTrait)
 */
class Context
{
    private static ?Connection $db = null;

    public static function withDatabase()
    {
        if (self::$db == null) {
            self::$db = depends('kwai.database', DatabaseDependency::class);
        }
        return self::$db;
    }

    private static function withUser(): UserEntity
    {
        return new UserEntity(
            1,
            new User(
                new UniqueId(),
                new EmailAddress('jigoro.kano@kwai.com'),
                new Name('Jigoro', 'Kano')
            )
        );
    }

    public static function createContext(): object
    {
        return (object)[
            'db' => self::withDatabase(),
            'user' => self::withUser()
        ];
    }

    public static function hasDatabase(): bool
    {
        return self::$db !== null;
    }
}

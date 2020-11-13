<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
namespace Kwai\Modules\Users\Presentation\Transformers;

use League\Fractal;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\UserAccount;

/**
 * A transformer for the User entity.
 */
class UserAccountTransformer extends Fractal\TransformerAbstract
{
    /**
     * The type of User
     */
    private static string $type = 'users';

    /**
     * Create a singular resource of a UserAccount entity
     * @param  Entity<UserAccount> $account The user account entity
     * @return Fractal\Resource\Item  A singular resource
     */
    public static function createForItem(Entity $account): Fractal\Resource\Item
    {
        return new Fractal\Resource\Item($account, new self(), self::$type);
    }

    /**
     * Create a collection of resources for a list of users.
     * @param  iterable $accounts A collection of users.
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $accounts): Fractal\Resource\Collection
    {
        return new Fractal\Resource\Collection($accounts, new self(), self::$type);
    }

    /**
     * Transforms a UserAccount entity to an array.
     * @param Entity<UserAccount> $account
     * @return array
     * @noinspection PhpUndefinedMethodInspection
     */
    public function transform(Entity $account): array
    {
        $user = $account->getUser();
        $traceableTime = $user->getTraceableTime();
        $lastLogin = $account->getLastLogin();
        $lastUnsuccessfulLogin = $account->getLastUnsuccessfulLogin();

        return [
            'id' => $account->id(),
            'uuid' => strval($user->getUuid()),
            'email' => strval($user->getEmailAddress()),
            'username' => strval($user->getUsername()),
            'remark' => $user->getRemark() ?? '',
            'created_at' => strval($traceableTime->getCreatedAt()),
            'last_login' => $lastLogin
                ? strval($lastLogin) : null,
            'last_unsuccessful_login' => $lastUnsuccessfulLogin
                ? strval($lastUnsuccessfulLogin) : null,
            'updated_at' => $traceableTime->isUpdated()
                ? strval($traceableTime->getUpdatedAt())
                : null
        ];
    }
}

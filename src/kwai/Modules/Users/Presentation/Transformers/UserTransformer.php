<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
namespace Kwai\Modules\Users\Presentation\Transformers;

use League\Fractal;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\User;

/**
 * A transformer for the User entity.
 */
class UserTransformer extends Fractal\TransformerAbstract
{
    /**
     * The type of User
     * @var string
     */
    private static $type = 'users';

    /**
     * Default includes
     * @var string[]
     */
    protected $defaultIncludes = [
        'abilities'
    ];

    /**
     * Create a singular resource of a User entity
     * @param  Entity<User> $user     The user entity
     * @return Fractal\Resource\Item  A singular resource
     */
    public static function createForItem(Entity $user): Fractal\Resource\Item
    {
        return new Fractal\Resource\Item($user, new self(), self::$type);
    }

    /**
     * Create a collection of resources for a list of users.
     * @param  iterable $users A collection of users.
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $users): Fractal\Resource\Collection
    {
        return new Fractal\Resource\Collection($users, new self(), self::$type);
    }

    /**
     * Get the included abilities
     * @param  Entity<User> $user The user containing the abilities.
     * @return Fractal\Resource\Collection
     */
    public function includeAbilities(Entity $user): Fractal\Resource\Collection
    {
        return AbilityTransformer::createForCollection($user->getAbilities());
    }

    /**
     * Transforms a User entity to an array.
     * @param Entity<User> $user
     * @return array
     */
    public function transform(Entity $user): array
    {
        $traceableTime = $user->getTraceableTime();
        $result = [
            'id' => $user->id(),
            'uuid' => strval($user->getUuid()),
            'email' => strval($user->getEmailAddress()),
            'username' => $user->getUsername(),
            'remark' => $user->getRemark(),
            'revoked' => $user->isRevoked(),
            'created_at' => strval($traceableTime->getCreatedAt())
        ];
        $result['last_login'] = $user->hasLastLogin()
            ? strval($user->getLastLogin())
            : null;
        $result['updated_at'] = $traceableTime->isUpdated()
            ? strval($traceableTime->getUpdatedAt())
            : null;

        return $result;
    }
}

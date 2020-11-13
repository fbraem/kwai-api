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
     */
    private static string $type = 'users';

    /**
     * The available includes
     */
    protected $availableIncludes = [
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
     *
     * @param  Entity<User> $user The user containing the abilities.
     * @return Fractal\Resource\Collection
     * @noinspection PhpUnused
     */
    public function includeAbilities(Entity $user): Fractal\Resource\Collection
    {
        /* @noinspection PhpUndefinedMethodInspection */
        return AbilityTransformer::createForCollection($user->getAbilities());
    }

    /**
     * Transforms a User entity to an array.
     * @param Entity<User> $user
     * @return array
     * @noinspection PhpUndefinedMethodInspection
     */
    public function transform(Entity $user): array
    {
        $traceableTime = $user->getTraceableTime();
        $result = [
            'id' => strval($user->getUuid()),
            'email' => strval($user->getEmailAddress()),
            'username' => strval($user->getUsername()),
            'remark' => $user->getRemark(),
            'created_at' => strval($traceableTime->getCreatedAt())
        ];
        $result['updated_at'] = $traceableTime->isUpdated()
            ? strval($traceableTime->getUpdatedAt())
            : null;

        return $result;
    }
}

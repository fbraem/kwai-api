<?php
/**
 * @package Kwai
 * @subpackage Users
 */
namespace Kwai\Modules\Users\Presentation\Transformers;

use League\Fractal;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * A transformer for the UserInvitation entity.
 */
class UserInvitationTransformer extends Fractal\TransformerAbstract
{
    /**
     * The type of User
     * @var string
     */
    private static $type = 'user_invitations';

    /**
     * Create a singular resource of a UserInvitation entity
     * @param  Entity<UserInvitation> $invitation The user invitation entity
     * @return Fractal\Resource\Item              A singular resource
     */
    public static function createForItem(Entity $invitation): Fractal\Resource\Item
    {
        return new Fractal\Resource\Item($invitation, new self(), self::$type);
    }

    /**
     * Create a collection of resources for a list of user invitations.
     * @param  iterable $invitations A collection of user invitations.
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $invitations): Fractal\Resource\Collection
    {
        return new Fractal\Resource\Collection($invitations, new self(), self::$type);
    }

    /**
     * Get the included mails
     * @param  Entity<UserInvitation> $invitation The user containing the abilities.
     * @return Fractal\Resource\Collection
     */
    public function includeMails(Entity $invitation): Fractal\Resource\Collection
    {
        // return AbilityTransformer::createForCollection($invitation->getMails());
    }

    /**
     * Transforms a User entity to an array.
     * @param Entity<UserInvitation> $invitation
     * @return array
     * @noinspection PhpUndefinedMethodInspection
     */
    public function transform(Entity $invitation): array
    {
        $traceableTime = $invitation->getTraceableTime();
        $result = [
            'id' => $invitation->id(),
            'uuid' => strval($invitation->getUuid()),
            'email' => strval($invitation->getEmailAddress()),
            'username' => $invitation->getName(),
            'remark' => $invitation->getRemark(),
            'expired_at' => strval($invitation->getExpiration()),
            'created_at' => strval($traceableTime->getCreatedAt()),
            'confirmed_at' => $invitation->isConfirmed() ? strval($invitation->getConfirmation()) : null
        ];
        $result['updated_at'] = $traceableTime->isUpdated()
            ? strval($traceableTime->getUpdatedAt())
            : null;

        return $result;
    }
}

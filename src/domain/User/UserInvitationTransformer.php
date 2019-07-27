<?php

namespace Domain\User;

use League\Fractal;

class UserInvitationTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'invitations';

    public static function createForItem(UserInvitation $invitation)
    {
        return new Fractal\Resource\Item($invitation, new self(), self::$type);
    }

    public static function createForCollection(iterable $users)
    {
        return new Fractal\Resource\Collection($users, new self(), self::$type);
    }

    public function transform(UserInvitation $invitation)
    {
        return $invitation->toArray();
    }
}

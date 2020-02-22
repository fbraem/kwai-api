<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class UserInvitationTable extends Table
{
    public function __construct()
    {
        parent::__construct('user_invitations', [
            'id',
            'email',
            'name',
            'token',
            'expired_at',
            'expired_at_timezone',
            'remark',
            'user_id',
            'created_at',
            'updated_at'
        ]);
    }
}

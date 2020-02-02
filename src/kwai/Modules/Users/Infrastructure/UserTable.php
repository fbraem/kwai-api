<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Table;

final class UserTable extends Table
{
    public function __construct()
    {
        parent::__construct('users', [
            'id',
            'email',
            'password',
            'last_login',
            'first_name',
            'last_name',
            'remark',
            'uuid',
            'created_at',
            'updated_at'
        ]);
    }
}

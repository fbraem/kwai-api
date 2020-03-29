<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class UserAbilitiesTable extends Table
{
    public function __construct()
    {
        parent::__construct('user_abilities', [
            'user_id',
            'ability_id',
            'created_at',
            'updated_at'
        ]);
    }
}

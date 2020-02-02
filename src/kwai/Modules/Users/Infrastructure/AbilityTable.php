<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Table;

final class AbilityTable extends Table
{
    public function __construct()
    {
        parent::__construct('abilities', [
            'id',
            'name',
            'remark',
            'created_at',
            'updated_at'
        ]);
    }
}

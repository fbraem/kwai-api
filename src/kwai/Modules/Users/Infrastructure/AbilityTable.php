<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\DefaultTable;

final class AbilityTable extends DefaultTable
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

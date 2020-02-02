<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Table;

final class RuleTable extends Table
{
    public function __construct()
    {
        parent::__construct('rules', [
            'id',
            'name',
            'remark',
            'created_at',
            'updated_at'
        ]);
    }
}

<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class RuleActionsTable extends Table
{
    public function __construct()
    {
        parent::__construct('rule_actions', [
            'id',
            'name',
            'remark',
            'created_at',
            'updated_at'
        ]);
    }
}

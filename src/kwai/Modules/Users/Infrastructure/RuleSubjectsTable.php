<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class RuleSubjectsTable extends Table
{
    public function __construct()
    {
        parent::__construct('rule_subjects', [
            'id',
            'name',
            'remark',
            'created_at',
            'updated_at'
        ]);
    }
}

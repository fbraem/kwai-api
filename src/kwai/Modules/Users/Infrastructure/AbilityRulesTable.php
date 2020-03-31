<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class AbilityRulesTable extends Table
{
    public function __construct()
    {
        parent::__construct('ability_rules', [
            'ability_id',
            'rule_id',
            'created_at',
            'updated_at'
        ]);
    }
}

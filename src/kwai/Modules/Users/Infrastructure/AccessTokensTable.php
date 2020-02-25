<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class AccessTokensTable extends Table
{
    public function __construct()
    {
        parent::__construct('oauth_access_tokens', [
            'id',
            'identifier',
            'expiration',
            'revoked',
            'created_at',
            'updated_at'
        ]);
    }
}
<?php

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class RefreshTokenTable extends Table
{
    public function __construct()
    {
        parent::__construct('oauth_refresh_tokens', [
            'id',
            'identifier',
            'access_token_id',
            'expiration',
            'revoked',
            'created_at',
            'updated_at'
        ]);
    }
}

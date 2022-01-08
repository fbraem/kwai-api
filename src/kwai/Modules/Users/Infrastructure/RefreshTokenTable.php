<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableAttribute;
use Kwai\Core\Infrastructure\Database\TableSchema;

/**
 * Class AccessTokenTable
 */
#[TableAttribute(name: 'oauth_refresh_tokens')]
class RefreshTokenTable extends TableSchema
{
    public ?int $id = null;
    public string $identifier;
    public ?int $access_token_id;
    public int $user_id;
    public string $expiration;
    public int $revoked;
    public string $created_at;
    public string $updated_at;
}

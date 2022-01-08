<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure;

/**
 * Class UserInvitationsTable
 */
class UserInvitationsTable extends \Kwai\Core\Infrastructure\Database\TableSchema
{
    public ?int $id = null;
    public string $email;
    public string $name;
    public string $uuid;
    public string $expired_at;
    public string $expired_at_timezone;
    public ?string $remark = null;
    public int $user_id;
    public string $created_at;
    public ?string $updated_at = null;
    public ?string $confirmed_at = null;
    public int $revoked;
}

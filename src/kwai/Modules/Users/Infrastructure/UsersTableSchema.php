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
 * Class UsersTableSchema
 */
#[TableAttribute(name: 'users')]
class UsersTableSchema extends TableSchema
{
    public ?int $id = null;
    public string $email;
    public string $password;
    public ?string $last_login = null;
    public string $first_name;
    public string $last_name;
    public ?string $remark = null;
    public ?int $member_id = null;
    public string $uuid;
    public string $created_at;
    public ?string $updated_at = null;
    public ?int $revoked = 0;
    public ?string $last_unsuccessful_login = null;
}

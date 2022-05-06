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
 * Class UsersTable
 */
#[TableAttribute(name: 'users')]
class UsersTable extends TableSchema
{
    public ?int $id;
    public string $email;
    public string $password;
    public ?string $last_login;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $remark;
    public ?int $member_id;
    public string $uuid;
    public string $created_at;
    public ?string $updated_at;
    public ?int $revoked;
    public ?string $last_unsuccessful_login;
    public bool $admin;
}

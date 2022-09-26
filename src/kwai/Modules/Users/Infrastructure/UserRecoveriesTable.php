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
 * Class UserRecoveriesTable
 */
#[TableAttribute(name: 'user_recoveries')]
class UserRecoveriesTable extends TableSchema
{
    public ?int $id = null;
    public int $user_id;
    public int $mail_id;
    public string $uuid;
    public string $expired_at;
    public string $expired_at_timezone;
    public ?string $confirmed_at = null;
    public ?string $remark = null;
    public string $created_at;
    public ?string $updated_at = null;
}

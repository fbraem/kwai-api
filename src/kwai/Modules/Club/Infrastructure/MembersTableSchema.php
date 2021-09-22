<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableAttribute;
use Kwai\Core\Infrastructure\Database\TableSchema;

/**
 * Class MembersTableSchema
 */
#[TableAttribute(name: 'sport_judo_members')]
class MembersTableSchema extends TableSchema
{
    public ?string $id = null;
    public string $license;
    public string $license_end_date;
    public string $person_id;
    public ?string $remark = null;
    public string $competition = '0';
    public string $created_at;
    public ?string $updated_at = null;
    public ?string $import_id = null;
    public string $active = '1';
}

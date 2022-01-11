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
 * Class ContactsTable
 */
#[TableAttribute(name: 'contacts')]
class ContactsTable extends TableSchema
{
    public ?int $id = null;
    public string $email;
    public string $tel;
    public string $mobile;
    public string $address;
    public string $postal_code;
    public string $city;
    public ?string $county = null;
    public int $country_id;
    public ?string $remark;
    public string $created_at;
    public ?string $updated_at;
}

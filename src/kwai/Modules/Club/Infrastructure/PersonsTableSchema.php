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
 * Class PersonsTableSchema
 */
#[TableAttribute(name: 'persons')]
class PersonsTableSchema extends TableSchema
{
    public ?string $id;
    public string $lastname;
    public string $firstname;
    public string $gender;
    public string $active = '1';
    public string $birthdate;
    public ?string $remark = null;
    public ?string $user_id = null;
    public ?string $contact_id = null;
    public ?string $code = null;
    public string $nationality_id;
    public string $created_at;
    public ?string $updated_at = null;
}

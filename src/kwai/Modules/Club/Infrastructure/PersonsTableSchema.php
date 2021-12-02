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
    public ?int $id = null;
    public string $lastname;
    public string $firstname;
    public int $gender;
    public int $active = 1;
    public string $birthdate;
    public ?string $remark = null;
    public ?int $user_id = null;
    public ?int $contact_id = null;
    public ?string $code = null;
    public int $nationality_id;
    public string $created_at;
    public ?string $updated_at = null;
}

<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableAttribute;
use Kwai\Core\Infrastructure\Database\TableSchema as TableSchemaAlias;

/**
 * Class PersonsTable
 */
#[TableAttribute(name: 'persons')]
class PersonsTable extends TableSchemaAlias
{
    public ?int $id = null;
    public string $firstname;
    public string $lastname;
}

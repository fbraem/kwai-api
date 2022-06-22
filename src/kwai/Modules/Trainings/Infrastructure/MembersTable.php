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
 * Class MembersTable
 */
#[TableAttribute(name: 'sport_judo_members')]
class MembersTable extends TableSchemaAlias
{
    public ?int $id = null;
}

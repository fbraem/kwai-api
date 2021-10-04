<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableAttribute;
use Kwai\Core\Infrastructure\Database\TableSchema;

#[TableAttribute(name: 'teams')]
class TeamsTableSchema extends TableSchema
{
    public ?string $id = null;
    public string $name;
    public ?string $season_id = null;
    public ?string $team_category_id = null;
    public string $active = '1';
    public ?string $remark = null;
    public string $created_at;
    public ?string $updated_at = null;
}

<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableEnum;

/**
 * Class Tables
 *
 * This class defines all tables for the Coaches module
 *
 * @method static Tables COACHES()
 * @method static Tables MEMBERS()
 * @method static Tables PERSONS()
 * @method static Tables USERS()
 */
class Tables extends TableEnum
{
    private const COACHES = 'coaches';
    private const MEMBERS = 'sport_judo_members';
    private const PERSONS = 'persons';
    private const USERS = 'users';
}

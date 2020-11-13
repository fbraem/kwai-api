<?php
/**
 * @package Modules
 * @subpackage Applications
 */

/* @noinspection PhpUnusedPrivateFieldInspection */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableEnum;

/**
 * Class Tables
 *
 * This class defines all table names for the Applications module.
 * @method static Tables APPLICATIONS()
 */
class Tables extends TableEnum
{
    private const APPLICATIONS = 'applications';
}

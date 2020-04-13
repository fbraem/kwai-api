<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Infrastructure;

use Closure;
use Kwai\Core\Infrastructure\Database\ColumnFilter;
use Kwai\Core\Infrastructure\Database\TableEnum;
use MyCLabs\Enum\Enum;
use function Latitude\QueryBuilder\alias;

/**
 * Class Tables
 *
 * This class defines all table names for the Mails module.
 * @method static Tables MAILS()
 * @method static Tables RECIPIENTS()
 */
class Tables extends TableEnum
{
    public const MAILS = 'mails';
    public const RECIPIENTS = 'recipients';
}

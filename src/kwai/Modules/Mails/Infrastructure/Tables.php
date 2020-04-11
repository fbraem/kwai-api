<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Infrastructure;

use Closure;
use Kwai\Core\Infrastructure\Database\ColumnFilter;
use MyCLabs\Enum\Enum;
use function Latitude\QueryBuilder\alias;

/**
 * Class Tables
 *
 * This class defines all table names for the Mails module.
 * @method static Tables MAILS()
 * @method static Tables RECIPIENTS()
 */
class Tables extends Enum
{
    public const MAILS = 'mails';
    public const RECIPIENTS = 'recipients';

    /**
     * @return Closure
     */
    public function getAliasFn()
    {
        return fn(string $column) =>
            alias(
                $this->getColumn($column),
                $this->getAlias($column)
            )
        ;
    }

    public function getColumn(string $column): string
    {
        return $this->getValue() . '.' . $column;
    }

    public function getAlias(string $column): string
    {
        return $this->getAliasPrefix() . $column;
    }

    private function getAliasPrefix(): string
    {
        return $this->getValue() . '_';
    }

    public function createColumnFilter(): ColumnFilter
    {
        return new ColumnFilter($this->getAliasPrefix());
    }

    public function __get($name): string
    {
        return $this->getColumn($name);
    }
}

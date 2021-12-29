<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use Illuminate\Support\Collection;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;

/**
 * Trait TableTrait
 */
Trait TableTrait
{
    public function alias(string $column, ?string $alias = null)
    {
        if ($alias === null) {
            return alias($this->column($column), $this->aliasPrefix() . $column);
        }
        return alias($this->column($column), $alias);
    }

    public function aliasPrefix(): string
    {
        return $this->value . '_';
    }

    public function aliases(string ...$columns): array
    {
        return array_map(
            fn ($column) => $this->alias($column),
            $columns
        );
    }

    public function field(string $column)
    {
        return field($this->column($column));
    }

    /**
     * Returns the column name prefixed with the tablename, separated with a dot.
     *
     * @param string $column
     * @return string
     */
    public function column(string $column): string
    {
        return $this->value . '.' . $column;
    }

    public function collect(Collection $row, ?string $prefix = null)
    {
        $prefix ??= $this->aliasPrefix();
        return $row->filter(
            fn ($item, $key) => $item !== null && str_starts_with($key, $prefix)
        )->mapWithKeys(
            fn ($item, $key) => [substr($key, strlen($prefix)) => $item]
        );
    }
}

<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use Illuminate\Support\Collection;
use Latitude\QueryBuilder\Builder\CriteriaBuilder;
use Latitude\QueryBuilder\ExpressionInterface;
use ReflectionClass;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;

/**
 * Class TableSchema
 *
 * Abstract class for a table schema.
 */
abstract class TableSchema
{
    /**
     * Constructor.
     *
     * When alias is omitted, the table name will be used as alias prefix.
     *
     * @param string|null $alias
     */
    public function __construct(
        private ?string $alias = null
    ) {
        $this->alias ??= static::getTableName();
    }

    /**
     * Gets the table name that is associated with the schema.
     * The table name should be defined using the TableAttribute at class level.
     *
     * @return string
     */
    public static function getTableName(): string
    {
        $ref = new ReflectionClass(static::class);
        $attributes = $ref->getAttributes(TableAttribute::class);
        if (count($attributes) > 0) {
            return $attributes[0]->getArguments()['name'] ?? '';
        }
        return '';
    }

    /**
     * Calls field for the given column.
     *
     * @param string $column
     * @return CriteriaBuilder
     */
    public function field(string $column): CriteriaBuilder
    {
        return field(self::getTableName() . '.' . $column);
    }

    /**
     * Returns an alias of the column.
     *
     * @param string $column
     * @return ExpressionInterface
     */
    public function alias(string $column): ExpressionInterface
    {
        return alias(self::getTableName() . '.' . $column, $this->alias . '_' . $column);
    }

    public function getAllAliases(): Collection
    {
        $ref = new ReflectionClass($this);
        return collect($ref->getProperties())
            ->map(fn ($item) => $this->alias($item->name));
    }

    /**
     * Map all items of the record to the schema properties.
     *
     * This only works with records that has used aliases to retrieve data.
     *
     * @param Collection $record
     */
    public function map(Collection $record)
    {
        $prefix = $this->alias . '_';
        $record->filter(
            fn ($item, $key) => str_starts_with($key, $prefix)
        )->mapWithKeys(
            fn ($item, $key) => [substr($key, strlen($prefix)) => $item]
        )->each(
            fn ($item, $key) => $this->$key = $item
        );
    }
}

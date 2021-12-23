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
    final public function __construct(
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
     * Returns the alias of the table (or the table name if no alias was set).
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Returns the column name with the table name.
     *
     * @param string $column
     * @return string
     */
    public function getColumn(string $column): string
    {
        return $this->alias . '.' . $column;
    }

    /**
     * Calls field for the given column.
     *
     * @param string $column
     * @return CriteriaBuilder
     */
    public function field(string $column): CriteriaBuilder
    {
        return field($this->getColumn($column));
    }

    /**
     * Returns an alias of the column.
     *
     * @param string $column
     * @return ExpressionInterface
     */
    public function aliasColumn(string $column): ExpressionInterface
    {
        return alias(
            $this->getColumn($column),
            $this->alias . '_' . $column
        );
    }

    public function getAllAliases(): Collection
    {
        $ref = new ReflectionClass($this);
        return collect($ref->getProperties())
            ->map(fn ($item) => $this->aliasColumn($item->name));
    }

    /**
     * Map all items of the record to the schema properties.
     *
     * This only works with records that has used aliases to retrieve data.
     *
     * @param Collection $record
     * @return static
     */
    public function map(Collection $record): static
    {
        $populatedSchema = new static($this->alias);

        $prefix = $this->alias . '_';
        $record->filter(
            fn ($item, $key) => str_starts_with($key, $prefix)
        )->mapWithKeys(
            fn ($item, $key) => [substr($key, strlen($prefix)) => $item]
        )->each(
            fn ($item, $key) => $populatedSchema->$key = $item
        );

        return $populatedSchema;
    }

    /**
     * An empty __set method, to avoid that unknown columns become part
     * of the schema.
     *
     * @param string $arg
     * @param $value
     */
    public function __set(string $arg, $value)
    {
    }
}

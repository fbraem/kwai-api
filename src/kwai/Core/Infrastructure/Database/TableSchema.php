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
        $this->alias ??= static::name();
    }

    /**
     * Gets the table name that is associated with the schema.
     * The table name should be defined using the TableAttribute at class level.
     *
     * @return string
     */
    public static function name(): string
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
    public static function field(string $column): CriteriaBuilder
    {
        return field(static::column($column));
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

    public static function column(string $column): string
    {
        return static::name() . '.' . $column;
    }

    public static function aliases(?string $alias = null): array
    {
        $schema = new static($alias);
        $ref = new ReflectionClass(static::class);
        return collect($ref->getProperties())
            ->map(fn ($item) => $schema->aliasColumn($item->name))
            ->toArray();
    }

    public function collect(string|array|null $forget = 'id'): Collection {
        $ref = new ReflectionClass($this);
        $data = collect($ref->getProperties())
            ->mapWithKeys(
                fn ($item) => [ $item->name => $item->getValue($this) ]
            )
        ;

        if ($forget) {
            return $data->forget($forget);
        }
        return $data;
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
     * Factory method that will create and map the record to this schema.
     *
     * @param Collection  $record
     * @param string|null $alias
     * @return static
     */
    public static function createFromRow(Collection $record, ?string $alias = null): static
    {
        $populatedSchema = new static($alias);
        return $populatedSchema->map($record);
   }

    /**
     * An empty __set method, to avoid that unknown columns become part
     * of the schema.
     *
     * @param string $arg
     * @param mixed $value
     */
    public function __set(string $arg, mixed $value)
    {
    }
}

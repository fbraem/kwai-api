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
    public function alias(string $column): ExpressionInterface
    {
        return alias(
            static::column($column),
            $this->alias . '_' . $column
        );
    }

    /**
     * Returns the column prefixed with the table name.
     *
     * @param string $column
     * @return string
     */
    public static function column(string $column): string
    {
        return static::name() . '.' . $column;
    }

    /**
     * Returns aliases for all the properties of the table.
     *
     * @param string|null $alias
     * @return array
     */
    public static function aliases(?string $alias = null): array
    {
        $schema = new static($alias);
        $ref = new ReflectionClass(static::class);
        return collect($ref->getProperties())
            ->map(fn ($item) => $schema->alias($item->name))
            ->toArray();
    }

    /**
     * Returns all properties of the table as a collection.
     * A property that is not initialized will be skipped.
     *
     * @return Collection
     */
    public function collect(): Collection {
        $ref = new ReflectionClass($this);
        return collect($ref->getProperties())
            ->filter(
                fn($item) => $item->isInitialized($this)
            )
            ->mapWithKeys(
                fn ($item) => [ $item->name => $item->getValue($this) ]
            )
        ;
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

        $prefix = strlen($this->alias) > 0 ? $this->alias . '_' : '';
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

<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\News\Domain\Category;
use League\Fractal;

/**
 * Class CategoryTransformer
 */
class CategoryTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'categories';

    /**
     * Create a singular resource of a Category entity
     *
     * @param Entity<Category> $category
     * @return Fractal\Resource\Item
     */
    public static function createForItem(Entity $category): Fractal\Resource\Item
    {
        return new Fractal\Resource\Item($category, new self(), self::$type);
    }

    /**
     * Create a collection of resources for a list of categories
     *
     * @param iterable $categories
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $categories): Fractal\Resource\Collection
    {
        return new Fractal\Resource\Collection($categories, new self(), self::$type);
    }

    /**
     * Transforms a category
     *
     * @param Entity<Category> $category
     * @return array
     */
    public function transform(Entity $category): array
    {
        return [
            'id' => $category->id(),
            'name' => $category->getName()
        ];
    }
}

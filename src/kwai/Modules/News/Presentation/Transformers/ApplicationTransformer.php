<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\News\Domain\Application;
use League\Fractal;

/**
 * Class ApplicationTransformer
 */
class ApplicationTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'applications';

    /**
     * Create a singular resource of a Application entity
     *
     * @param Entity<Application> $category
     * @return Fractal\Resource\Item
     */
    public static function createForItem(Entity $category): Fractal\Resource\Item
    {
        return new Fractal\Resource\Item($category, new self(), self::$type);
    }

    /**
     * Create a collection of resources for a list of applications
     *
     * @param iterable $applications
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $applications): Fractal\Resource\Collection
    {
        return new Fractal\Resource\Collection($applications, new self(), self::$type);
    }

    /**
     * Transforms a category
     *
     * @param Entity<Application> $application
     * @return array
     */
    public function transform(Entity $application): array
    {
        return [
            'id' => $application->id(),
            'name' => $application->getName()
        ];
    }
}

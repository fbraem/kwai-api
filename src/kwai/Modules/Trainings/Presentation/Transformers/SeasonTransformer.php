<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Season;
use League\Fractal;

/**
 * Class SeasonTransformer
 */
class SeasonTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'seasons';

    /**
     * Create a single resource of a Season entity.
     *
     * @param Entity<Season> $season
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Entity $season,
    ): Fractal\Resource\Item {
        return new Fractal\Resource\Item(
            $season,
            new self(),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of seasons.
     *
     * @param iterable $seasons
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(
        iterable $seasons,
    ): Fractal\Resource\Collection {
        return new Fractal\Resource\Collection(
            $seasons,
            new self(),
            self::$type
        );
    }

    /**
     * Transform a Season entity into an array
     *
     * @param Entity<Season> $season
     * @return array
     */
    public function transform(Entity $season): array
    {
        return [
            'id' => (string) $season->id(),
            'name' => $season->getName()
        ];
    }
}

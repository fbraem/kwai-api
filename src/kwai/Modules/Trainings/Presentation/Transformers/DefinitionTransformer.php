<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Definition;
use League\Fractal;

/**
 * Class DefinitionTransformer
 *
 * Transforms a Definition entity to JSONAPI
 */
class DefinitionTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'definitions';

    protected $defaultIncludes = [
        'season',
        'team'
    ];

    /**
     * Create a single resource of a Definition entity.
     *
     * @param Entity<Definition> $definition
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Entity $definition,
    ): Fractal\Resource\Item {
        return new Fractal\Resource\Item(
            $definition,
            new self(),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of definitions.
     *
     * @param iterable         $definitions
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(
        iterable $definitions,
    ): Fractal\Resource\Collection {
        return new Fractal\Resource\Collection(
            $definitions,
            new self(),
            self::$type
        );
    }

    /**
     * Include season
     *
     * @param Entity<Definition> $definition
     * @return Fractal\Resource\Item
     */
    public function includeSeason(Entity $definition)
    {
        $season = $definition->getSeason();
        if ($season) {
            return SeasonTransformer::createForItem($season);
        }
        return null;
    }

    /**
     * Include team
     *
     * @param Entity<Definition> $definition
     * @return Fractal\Resource\Item
     */
    public function includeTeam(Entity $definition)
    {
        $team = $definition->getTeam();
        if ($team) {
            return TeamTransformer::createForItem($team);
        }
        return null;
    }

    /**
     * @param Entity<Definition> $definition
     * @return array
     */
    public function transform(Entity $definition): array
    {
        /* @var Definition $definition */
        $traceableTime = $definition->getTraceableTime();

        $result = [
            'id' => strval($definition->id()),
            'name' => $definition->getName(),
            'description' => $definition->getDescription(),
            'weekday' => $definition->getWeekday()->getValue(),
            'start_time' => strval($definition->getStartTime()),
            'end_time' => strval($definition->getEndTime()),
            'time_zone' => $definition->getStartTime()->getTimezone(),
            'active' => $definition->isActive(),
            'location' => $definition->getLocation() ? strval($definition->getLocation()) : null,
            'remark' => $definition->getRemark(),
            'created_at' => strval($traceableTime->getCreatedAt())
        ];
        $result['updated_at'] = $traceableTime->getUpdatedAt()?->format();

        return $result;
    }
}

<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Presentation\Transformers;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Club\Domain\Team;
use League\Fractal;
/**
 * Class TeamTransformer
 */
class TeamTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'teams';

    private function __construct()
    {
    }

    /**
     * Create a single resource of a Coach entity.
     *
     * @param Entity<Team> $team
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Entity $team
    ): Fractal\Resource\Item {
        return new Fractal\Resource\Item(
            $team,
            new self(),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of coaches.
     *
     * @param iterable $teams
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(
        iterable $teams
    ): Fractal\Resource\Collection {
        return new Fractal\Resource\Collection(
            $teams,
            new self(),
            self::$type
        );
    }

    /**
     * Transform a Member entity into an array
     *
     * @param Entity<Team> $team
     * @return array
     */
    public function transform(Entity $team): array
    {
        $traceableTime = $team->getTraceableTime();

        $result = [
            'id' => (string) $team->id(),
            'name' => (string) $team->getName(),
            'created_at' => strval($traceableTime->getCreatedAt()),
        ];
        $result['updated_at'] = $traceableTime->isUpdated()
            ? strval($traceableTime->getUpdatedAt())
            : null
        ;

        return $result;
    }
}

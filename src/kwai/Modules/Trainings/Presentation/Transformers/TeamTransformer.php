<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Team;
use League\Fractal;

/**
 * Class TeamTransformer
 */
class TeamTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'teams';

    /**
     * Create a single resource of a Team entity.
     *
     * @param Entity<Team> $team
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Entity $team,
    ): Fractal\Resource\Item {
        return new Fractal\Resource\Item(
            $team,
            new self(),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of teams.
     *
     * @param iterable $teams
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(
        iterable $teams,
    ): Fractal\Resource\Collection {
        return new Fractal\Resource\Collection(
            $teams,
            new self(),
            self::$type
        );
    }

    /**
     * Transform a Team entity into an array
     *
     * @param Entity<Team> $team
     * @return array
     */
    public function transform(Entity $team): array
    {
        return [
            'id' => (string) $team->id(),
            'name' => $team->getName()
        ];
    }
}

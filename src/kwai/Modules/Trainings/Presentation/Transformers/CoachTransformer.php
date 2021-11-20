<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use League\Fractal;

/**
 * Class CoachTransformer
 */
class CoachTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'coaches';

    /**
     * Create a single resource of a Coach entity.
     *
     * @param Entity<Coach> $coach
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Entity $coach,
    ): Fractal\Resource\Item {
        return new Fractal\Resource\Item(
            $coach,
            new self(),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of coaches.
     *
     * @param iterable $coaches
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(
        iterable $coaches,
    ): Fractal\Resource\Collection {
        return new Fractal\Resource\Collection(
            $coaches,
            new self(),
            self::$type
        );
    }

    /**
     * Transform a Coach entity into an array
     *
     * @param Entity<Coach> $coach
     * @return array
     */
    public function transform(Entity $coach): array
    {
        return [
            'id' => (string) $coach->id(),
            'name' => (string) $coach->getName()
        ];
    }
}

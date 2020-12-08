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
     * @param Entity<Coach>|TrainingCoach $coach
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Entity|TrainingCoach $coach,
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
     * Transform a Team entity into an array
     *
     * @param Entity<Coach>|TrainingCoach $coach
     * @return array
     */
    public function transform(Entity|TrainingCoach $coach): array
    {
        if ($coach instanceof TrainingCoach) {
            return [
                'id' => $coach->getCoach()->id(),
                'name' => (string) $coach->getCoach()->getName(),
                'present' => $coach->isPresent(),
                'payed' => $coach->isPayed(),
                'head' => $coach->isHead()
            ];
        }
        return [
            'id' => (string) $coach->id(),
            'name' => (string) $coach->getName()
        ];
    }
}

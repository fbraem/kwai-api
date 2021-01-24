<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Transformers;

use Kwai\Modules\Trainings\Domain\ValueObjects\Presence;
use League\Fractal;

/**
 * Class PresenceTransformer
 */
class PresenceTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'presences';

    /**
     * Create a single resource of a Coach entity.
     *
     * @param Presence $presence
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Presence $presence,
    ): Fractal\Resource\Item {
        return new Fractal\Resource\Item(
            $presence,
            new self(),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of coaches.
     *
     * @param iterable $presences
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(
        iterable $presences,
    ): Fractal\Resource\Collection {
        return new Fractal\Resource\Collection(
            $presences,
            new self(),
            self::$type
        );
    }

    public function transform(Presence $presence): array
    {
        $member = $presence->getMember();
        /** @noinspection PhpUndefinedMethodInspection */
        return [
            'id' => $member->id(),
            'remark' => $presence->getRemark(),
            'name' => (string) $member->getName(),
            'gender' => $member->getGender(),
            'birthdate' => (string) $member->getBirthDate(),
            'license' => $member->getLicense(),
            'license_end_date' => (string) $member->getLicenseEndDate(),
            'created_at' => (string) $presence->getTraceableTime()->getCreatedAt(),
            'updated_at' => $presence->getTraceableTime()->getUpdatedAt()?->__toString()
        ];
    }
}

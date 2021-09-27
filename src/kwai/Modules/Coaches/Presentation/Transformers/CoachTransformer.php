<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Coaches\Domain\Coach;
use League\Fractal;

/**
 * Class CoachTransformer
 */
class CoachTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'coaches';

    protected $defaultIncludes = [
        'member'
    ];

    private function __construct(
        private ?Entity $user = null
    ) {
    }

    /**
     * Create a single resource of a Coach entity.
     *
     * @param Entity<Coach> $coach
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Entity $coach,
        ?Entity $user = null
    ): Fractal\Resource\Item {
        return new Fractal\Resource\Item(
            $coach,
            new self($user),
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
        ?Entity $user = null
    ): Fractal\Resource\Collection {
        return new Fractal\Resource\Collection(
            $coaches,
            new self($user),
            self::$type
        );
    }

    public function includeMember(Entity $coach)
    {
        return MemberTransformer::createForItem($coach->getMember());
    }

    /**
     * Transform a Coach entity into an array
     *
     * @param Entity<Coach> $coach
     * @return array
     */
    public function transform(Entity $coach): array
    {
        $traceableTime = $coach->getTraceableTime();

        $result = [
            'id' => (string) $coach->id(),
            'bio' => $coach->getBio(),
            'diploma' => $coach->getDiploma() ?? null,
            'active' => $coach->isActive(),
            'remark' => $coach->getRemark(),
            'created_at' => strval($traceableTime->getCreatedAt())
        ];
        if ($this->user) {
            $result['owner'] = $coach->getUser()?->id() === $this->user->id();
        } else {
            $result['owner'] = false;
        }
        $result['updated_at'] = $traceableTime->isUpdated()
            ? strval($traceableTime->getUpdatedAt())
            : null
        ;

        return $result;
    }
}

<?php
/**
 * @package Kwai/Modules
 * @subpackage User
 */
namespace Kwai\Modules\Users\Presentation\Transformers;

use League\Fractal;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\Ability;

/**
 * A transformer for the Ability entity.
 */
class AbilityTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'abilities';

    protected $defaultIncludes = [
        'rules'
    ];

    /**
     * Create a singular resource of an Ability entity
     * @param  Entity<Ability> $ability   The ability entity
     * @return Fractal\Resource\Item      A singular resource
     */
    public static function createForItem(Entity $ability): Fractal\Resource\Item
    {
        return new Fractal\Resource\Item($ability, new self(), self::$type);
    }

    /**
     * Create a collection of resources for a list of abilities.
     * @param  iterable $abilities A collection of abilities.
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $abilities): Fractal\Resource\Collection
    {
        return new Fractal\Resource\Collection(
            $abilities,
            new self(),
            self::$type
        );
    }

    /**
     * Get the included rules
     * @param  Entity $ability The ability containing the rules.
     * @return Fractal\Resource\Collection
     */
    public function includeRules(Entity $ability): Fractal\Resource\Collection
    {
        return RuleTransformer::createForCollection($ability->getRules());
    }

    /**
     * Transforms an Ability entity to an array.
     * @param Entity<Ability> $ability
     * @return array
     */
    public function transform(Entity $ability): array
    {
        $traceableTime = $ability->getTraceableTime();
        $result = [
            'id' => $ability->id(),
            'name' => strval($ability->getName()),
            'remark' => $ability->getRemark(),
            'created_at' => strval($traceableTime->getCreatedAt())
        ];
        $result['updated_at'] = $traceableTime->isUpdated()
            ? strval($traceableTime->getUpdatedAt())
            : null;
        return $result;
    }
}

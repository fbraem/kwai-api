<?php
/**
 * @package Kwai/Modules
 * @subpackage User
 */
namespace Kwai\Modules\Users\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\Ability;
use League\Fractal;

/**
 * A transformer for the Ability entity.
 */
class AbilityTransformer extends Fractal\TransformerAbstract
{
    /**
     * The JSON-API type
     */
    private static string $type = 'abilities';

    /**
     * The default includes
     * @var string[]
     */
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
     * @param  Entity<Ability> $ability The ability containing the rules.
     * @return Fractal\Resource\Collection
     */
    public function includeRules(Entity $ability): Fractal\Resource\Collection
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return RuleTransformer::createForCollection($ability->getRules());
    }

    /**
     * Transforms an Ability entity to an array.
     * @param Entity<Ability> $ability
     * @return array
     * @noinspection PhpUndefinedMethodInspection
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

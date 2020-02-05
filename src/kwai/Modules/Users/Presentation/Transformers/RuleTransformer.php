<?php
/**
 * @package Kwai/Modules
 * @subpackage User
 */
namespace Kwai\Modules\Users\Presentation\Transformers;

use League\Fractal;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\Rule;

/**
 * A transformer for a Rule entity
 */
class RuleTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'rules';

    /**
     * Create a singular resource of a Rule entity
     * @param  Entity<Rule> $rule     The rule entity
     * @return Fractal\Resource\Item  A singular resource
     */
    public static function createForItem(Entity $rule): Fractal\Resource\Item
    {
        return new Fractal\Resource\Item($rule, new self(), self::$type);
    }

    /**
     * Create a collection of resources for a list of rules.
     * @param  iterable $rules A collection of rules.
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $rules): Fractal\Resource\Collection
    {
        return new Fractal\Resource\Collection($rules, new self(), self::$type);
    }

    /**
     * Transforms a Rule entity to an array.
     * @param Entity<Rule> $rule
     * @return array
     */
    public function transform(Entity $rule): array
    {
        $traceableTime = $rule->getTraceableTime();
        $result = [
            'id' => $rule->id(),
            'name' => strval($rule->getName()),
            'action' => $rule->getAction(),
            'subject' => $rule->getSubject(),
            'remark' => $rule->getRemark(),
            'created_at' => strval($traceableTime->getCreatedAt())
        ];
        $result['updated_at'] = $traceableTime->isUpdated()
            ? strval($traceableTime->getUpdatedAt())
            : null;
        return $result;
    }
}

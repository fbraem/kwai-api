<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;

use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Domain\RuleEntity;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTable;

final class RuleDTO
{
    public function __construct(
        public RulesTable $rule = new RulesTable(),
        public RuleSubjectsTable $subject = new RuleSubjectsTable()
    ) {
    }

    /**
     * Create a Rule domain object from a database row.
     *
     * @return Rule
     */
    public function create(): Rule
    {
        return new Rule(
            name: $this->rule->name,
            subject: $this->subject->name,
            permission: $this->rule->permission,
            remark: $this->rule->remark,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->rule->created_at),
                $this->rule->updated_at
                ? Timestamp::createFromString($this->rule->updated_at)
                : null
            )
        );
    }

    /**
     * Create a Rule entity from a database row.
     *
     * @return RuleEntity
     */
    public function createEntity(): RuleEntity
    {
        return new RuleEntity(
            $this->rule->id,
            $this->create()
        );
    }

    /**
     * Persist a Rule domain object to a database row.
     *
     * @param Rule $rule
     * @return $this
     */
    public function persist(Rule $rule): RuleDTO
    {
        $this->rule->name = $rule->getName();
        $this->rule->permission = $rule->getPermission();
        $this->rule->remark = $rule->getRemark();
        return $this;
    }

    /**
     * Persist a Rule entity to a database row.
     *
     * @param RuleEntity $rule
     * @return $this
     */
    public function persistEntity(RuleEntity $rule): RuleDTO
    {
        $this->rule->id = $rule->id();
        return $this->persist($rule->domain());
    }
}

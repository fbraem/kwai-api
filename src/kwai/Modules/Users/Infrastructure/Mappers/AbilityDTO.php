<?php
/**
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Infrastructure\AbilitiesTable;

final class AbilityDTO
{
    /**
     * @param AbilitiesTable      $ability
     * @param Collection<RuleDTO> $rules
     */
    public function __construct(
        public AbilitiesTable $ability = new AbilitiesTable(),
        public Collection     $rules = new Collection()
    ) {
    }

    /**
     * Create an Ability domain object from a database row.
     *
     * @return Ability
     */
    public function create(): Ability
    {
        return new Ability(
            name: $this->ability->name,
            remark: $this->ability->remark,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->ability->created_at),
                $this->ability->updated_at
                    ? Timestamp::createFromString($this->ability->updated_at)
                    : null
            ),
            rules: $this->rules
                ->map(
                    fn (RuleDTO $ruleDto) => $ruleDto->createEntity()
                )
        );
    }

    /**
     * Create an Ability entity from a database row
     *
     * @return Entity<Ability>
     */
    public function createEntity(): Entity
    {
        return new Entity(
            $this->ability->id,
            $this->create()
        );
    }

    /**
     * Persist the Ability domain object to a database row.
     *
     * @param Ability $ability
     * @return AbilityDTO
     */
    public function persist(Ability $ability): static
    {
        $this->ability->name = $ability->getName();
        $this->ability->remark = $ability->getRemark();
        $this->ability->created_at = (string) $ability->getTraceableTime()->getCreatedAt();
        if ($ability->getTraceableTime()->isUpdated()) {
            $this->ability->updated_at = (string) $ability->getTraceableTime()->getUpdatedAt();
        }
        $this->rules = $ability->getRules()->map(
            static fn(Entity $rule) => (new RuleDTO())->persistEntity($rule)
        );
        return $this;
    }

    /**
     * Persist the Ability entity to a database row.
     *
     * @param Entity<Ability> $ability
     * @return $this
     */
    public function persistEntity(Entity $ability): static
    {
        $this->ability->id = $ability->id();
        return $this->persist($ability->domain());
    }
}

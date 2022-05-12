<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Security;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\JSONAPI;

/**
 * Class RuleResource
 */
#[JSONAPI\Resource(type: 'rules', id: 'getId')]
class RuleResource
{
    /**
     * @param UniqueId         $uuid
     * @param Collection<Rule> $rules
     */
    public function __construct(
        private UniqueId $uuid,
        private Collection $rules
    ) {
    }

    public function getId(): string
    {
        return (string) $this->uuid;
    }

    #[JSONAPI\Attribute(name: 'rules')]
    public function getSubject(): array
    {
        return $this->rules->map(static fn (Rule $rule) => [
            'subject' => $rule->getSubject(),
            'action' => $rule->getAction(),
            'fields' => $rule->getFields(),
            'conditions' => $rule->getConditions()
        ])->toArray();
    }
}

<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Security;

/**
 * Class Rule
 *
 * A class for representing CASL rules.
 * @see https://casl.js.org
 */
class Rule
{
    public function __construct(
        private string $subject,
        private string $action,
        private array $fields = [],
        private ?object $conditions = null
    ) {
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getConditions(): object
    {
        return $this->conditions;
    }
}

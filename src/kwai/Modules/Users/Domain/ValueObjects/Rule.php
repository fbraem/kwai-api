<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain\ValueObjects;

/**
 * Rule valueobject
 */
class Rule
{
    /**
     * Action of the rule
     * @var string
     */
    private $action;

    /**
     * Subject of the rule
     * @var string
     */
    private $subject;

    /**
     * Constructor
     * @param Subject $subject
     * @param Action  $action
     */
    public function __construct(string $subject, string $action)
    {
        $this->subject = $subject;
        $this->action = $action;
    }

    /**
     * Returns the action
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Returns the subject
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
}

<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * BrowseRulesCommand
 */
class BrowseRulesCommand
{
    /**
     * Only return the rules of the given subject.
     */
    public ?string $subject = null;
}

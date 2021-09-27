<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\UseCases;

/**
 * Class BrowseMembersCommand
 *
 * Command for the use case BrowseMembers.
 */
class BrowseMembersCommand
{
    public ?int $limit = null;
    public ?int $offset = null;
}

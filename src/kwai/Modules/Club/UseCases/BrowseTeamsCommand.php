<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\UseCases;

/**
 * Class BrowseTeamsCommand
 *
 * Command for the use case BrowseTeams
 */
class BrowseTeamsCommand
{
    public ?int $limit = null;
    public ?int $offset = null;
}

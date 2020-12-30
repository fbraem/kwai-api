<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\UseCases;

/**
 * Class BrowseCoachesCommand
 *
 * Command for the use case Browse Coaches
 */
class BrowseCoachesCommand
{
    public bool $active = true;

    public ?int $limit = null;

    public ?int $offset = null;
}

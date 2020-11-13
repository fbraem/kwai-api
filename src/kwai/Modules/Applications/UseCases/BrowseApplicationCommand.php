<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\UseCases;

/**
 * Class BrowseApplicationCommand
 *
 * Command for the BrowseApplication use case.
 */
class BrowseApplicationCommand
{
    public ?string $app = null;
}

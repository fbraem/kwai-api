<?php
/**
 * @package Pages
 * @subpackage UseCases
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\UseCases;

/**
 * Class BrowsePagesCommand
 *
 * Command for use case BrowsePages
 */
class BrowsePagesCommand
{
    private ?int $limit = null;
    private ?int $offset = null;
    private ?int $application = null;
    private bool $enabled = true;
}

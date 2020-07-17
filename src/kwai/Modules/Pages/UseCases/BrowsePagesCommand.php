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
    public ?int $limit = null;
    public ?int $offset = null;
    /**
     * @var int|string|null
     */
    public $application = null;
    public bool $enabled = true;
}
<?php
/**
 * @package Modules
 * @subpackage Pages
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
    public int|string|null $application = null;
    public bool $enabled = true;

    const SORT_NONE = 0;
    const SORT_PRIORITY = 1;
    const SORT_APPLICATION = 2;
    const SORT_CREATION_DATE = 3;

    /**
     * @var int
     */
    public int $sort = self::SORT_NONE;

    public ?string $userUid = null;
}

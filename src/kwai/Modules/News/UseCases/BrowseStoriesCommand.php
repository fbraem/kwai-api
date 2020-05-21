<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

/**
 * Class BrowseStoriesCommand
 *
 * Command for the use case BrowseStories
 */
class BrowseStoriesCommand
{
    public ?int $limit = null;
    public ?int $offset = null;
    public bool $enabled = true;
    public ?int $publishYear = null;
    public ?int $publishMonth = null;
    public ?int $application = null;
    public bool $promoted = false;
}

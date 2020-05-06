<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

/**
 * Class CreateStoryCommand
 *
 * Command for the use case CreateStory
 */
class CreateStoryCommand
{
    /**
     * The id of the category for this story.
     */
    public int $category;

    /**
     * The publication date
     */
    public string $publish_date;

    /**
     * The timezone used for publish_date and end_date
     */
    public string $timezone;

    /**
     * A date to indicate the end of the publication
     */
    public ?string $end_date = null;

    /**
     * Is this story promoted? 0 means no, > 0 means the priority.
     */
    public int $promoted = 0;

    /**
     * When does the promotion end?
     */
    public ?string $promotion_end_date = null;

    /**
     * Is this story enabled?
     */
    public bool $enabled = true;

    /**
     * A remark
     */
    public ?string $remark = null;

    /**
     * The content of the story.
     * @var Content[]
     */
    public array $contents = [];
}

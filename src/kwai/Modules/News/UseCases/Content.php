<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

/**
 * Class Content
 *
 * Defines the content structure for stories.
 */
class Content
{
    /**
     * The title
     */
    public string $title;

    /**
     * Language code
     */
    public string $locale = 'nl';

    /**
     * The format of the content
     */
    public string $format = 'md';

    /**
     * Summary of the story
     */
    public string $summary;

    /**
     * The content of the story
     */
    public ?string $content;

    /**
     * The id of the author of the content
     */
    public string $author;
}

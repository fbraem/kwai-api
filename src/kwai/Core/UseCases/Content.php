<?php
/**
 * @package Core
 * @subpackage UseCases
 */
declare(strict_types=1);

namespace Kwai\Core\UseCases;

/**
 * Class Content
 *
 * Helper class for commands that uses the Text value object like
 * news stories, pages, ...
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
}

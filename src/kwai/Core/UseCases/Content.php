<?php
/**
 * @package Core
 * @subpackage UseCases
 */
declare(strict_types=1);

namespace Kwai\Core\UseCases;

use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;

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
    public Locale $locale = Locale::NL;

    /**
     * The format of the content
     */
    public DocumentFormat $format = DocumentFormat::MARKDOWN;

    /**
     * Summary of the story
     */
    public string $summary;

    /**
     * The content of the story
     */
    public ?string $content;
}

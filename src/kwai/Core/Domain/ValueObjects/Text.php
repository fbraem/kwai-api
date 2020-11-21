<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use Kwai\Core\Domain\Entity;

/**
 * Class Text
 *
 * A value object for text. Use it for news stories, articles, events, ...
 */
class Text
{
    /**
     * The language locale of the text.
     */
    private Locale $locale;

    /**
     * The format of the text
     */
    private DocumentFormat $format;

    /**
     * A title
     */
    private string $title;

    /**
     * A summary
     */
    private string $summary;

    /**
     * The real content of the text.
     */
    private ?string $content;

    /**
     * The author of the text.
     */
    private Creator $author;

    /**
     * Text constructor.
     *
     * @param Locale         $locale
     * @param DocumentFormat $format
     * @param string         $title
     * @param string         $summary
     * @param string|null    $content
     * @param Creator        $author
     */
    public function __construct(
        Locale $locale,
        DocumentFormat $format,
        string $title,
        string $summary,
        ?string $content,
        Creator $author
    ) {
        $this->locale = $locale;
        $this->format = $format;
        $this->title = $title;
        $this->summary = $summary;
        $this->content = $content;
        $this->author = $author;
    }

    /**
     * Return the locale
     */
    public function getLocale(): Locale
    {
        return $this->locale;
    }

    /**
     * Return the document format
     */
    public function getFormat(): DocumentFormat
    {
        return $this->format;
    }

    /**
     * Return the title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Return the summary
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * Return the content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Return the author
     */
    public function getAuthor(): Creator
    {
        return $this->author;
    }
}

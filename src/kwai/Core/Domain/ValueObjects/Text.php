<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Modules\Users\Domain\User;

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
     * @var Entity<User>
     */
    private Entity $author;

    /**
     * Text constructor.
     *
     * @param Locale         $locale
     * @param DocumentFormat $format
     * @param string         $title
     * @param string         $summary
     * @param string|null    $content
     * @param Entity         $author
     */
    public function __construct(
        Locale $locale,
        DocumentFormat $format,
        string $title,
        string $summary,
        ?string $content,
        Entity $author
    ) {
        $this->locale = $locale;
        $this->format = $format;
        $this->title = $title;
        $this->summary = $summary;
        $this->content = $content;
        $this->author = $author;
    }
}

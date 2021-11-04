<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Converter;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverterInterface;

/**
 * Class MarkdownConverter
 *
 * Converts markdown to HTML
 */
class MarkdownConverter implements Converter
{
    /**
     * The PHP league commonmark converter
     */
    private MarkdownConverterInterface $converter;

    /**
     * MarkdownConverter constructor.
     */
    public function __construct()
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $this->converter = new \League\CommonMark\MarkdownConverter($environment);
    }

    /**
     * @inheritDoc
     */
    public function convert(string $text): string
    {
        return trim((string) $this->converter->convertToHtml($text));
    }
}

<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Converter;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
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
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new TableExtension());
        $this->converter = new CommonMarkConverter([], $environment);
    }

    /**
     * @inheritDoc
     */
    public function convert(string $text): string
    {
        return trim($this->converter->convertToHtml($text));
    }
}

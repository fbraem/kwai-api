<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Converter\MarkdownConverter;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;

/**
 * Class ConvertDependency
 */
class ConvertDependency implements Dependency
{
    public function create(): ConverterFactory
    {
        $factory = new ConverterFactory();
        $factory->register(DocumentFormat::MARKDOWN->value, MarkdownConverter::class);
        return $factory;
    }
}

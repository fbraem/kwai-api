<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Core\Infrastructure\Converter;

use Kwai\Core\Infrastructure\Converter\Converter;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Converter\MarkdownConverter;
use PHPUnit\Framework\TestCase;

class ConverterFactoryTest extends TestCase
{
    public function testCreateConverter(): Converter
    {
        $factory = new ConverterFactory();
        $factory->register('md', MarkdownConverter::class);
        $converter = $factory->createConverter('md');
        $this->assertInstanceOf(
            MarkdownConverter::class,
            $converter,
            'This must be a markdown converter'
        );
        return $converter;
    }

    /**
    /* @depends testCreateConverter
     * @param Converter $converter
     */
    public function testConvert(Converter $converter)
    {
        $html = $converter->convert('**TEST**');
        $this->assertEquals('<p><strong>TEST</strong></p>', $html, 'Invalid HTML generated');
    }
}

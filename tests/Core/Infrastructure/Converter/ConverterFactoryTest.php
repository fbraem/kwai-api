<?php
declare(strict_types=1);

namespace Tests\Core\Infrastructure\Converter;

use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Converter\MarkdownConverter;

it('can create a converter', function () {
    $factory = new ConverterFactory();
    $factory->register('md', MarkdownConverter::class);
    $converter = $factory->createConverter(DocumentFormat::MARKDOWN);
    expect($converter)->toBeInstanceOf(MarkdownConverter::class);
    return $converter;
});

// TODO: https://github.com/pestphp/pest/issues/213
// "it" must repeated in the depends method
it('can convert markdown', function ($converter) {
    $html = $converter->convert('**TEST**');
    expect($html)->toBe('<p><strong>TEST</strong></p>');
})->depends('it can create a converter');

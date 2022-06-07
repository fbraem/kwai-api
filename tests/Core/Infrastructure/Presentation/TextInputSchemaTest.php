<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Infrastructure\Presentation\TextInputSchema;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;

it('can process a text', function () {
    $schema = new TextInputSchema();
    $processor = new Processor();
    $content = $processor->process(
        $schema->create(),
        (object)[
            'title' => 'Test',
            'locale' => 'nl',
            'format' => 'md',
            'summary' => 'This is a test',
            'content' => 'This is the content of the test'
        ]
    );
    expect($content->title)->toBe('Test');
});

it('can set a default format', function () {
    $schema = new TextInputSchema();
    $processor = new Processor();
    $normalized = $processor->process(
        $schema->create(),
        (object)[
            'title' => 'Test',
            'locale' => 'nl',
            'summary' => 'This is a test',
            'content' => 'This is the content of the test'
        ]
    );
    $content = $schema->process($normalized);
    expect($content->format)->toBe(DocumentFormat::MARKDOWN->value);
});

it('throws an exception when required value is missing', function () {
    $schema = new TextInputSchema();
    $processor = new Processor();
    $processor->process(
        $schema->create(),
        (object)[
            'locale' => 'nl',
            'summary' => 'This is a test',
            'content' => 'This is the content of the test'
        ]
    );
})->throws(ValidationException::class);

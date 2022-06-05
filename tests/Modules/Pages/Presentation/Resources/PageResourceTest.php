<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Converter\MarkdownConverter;
use Kwai\JSONAPI;
use Kwai\Modules\Pages\Domain\Application;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Presentation\Resources\PageResource;

it('can serialize a page resource', function () {
    $application = new Entity(
        1,
        new Application(
            title: 'This is a test',
            name: 'test'
        )
    );
    $page = new Entity(
        1,
        new Page(
            application: $application,
            enabled: true,
            priority: 1,
            remark: 'This is a test'
        )
    );
    $page->addContent(new Text(
        locale: Locale::NL,
        format: DocumentFormat::MARKDOWN,
        title: 'Test page',
        author: new Creator(
            id: 1,
            name: new Name('Jigoro', 'Kano')
        ),
        summary: 'This is a test',
        content: 'This is a test'
    ));

    $converterFactory = new ConverterFactory();
    $converterFactory->register('md', MarkdownConverter::class);
    $resource = new PageResource($page, $converterFactory);

    try {
        $jsonapi = JSONAPI\Document::createFromObject($resource)->serialize();
    } catch (JSONAPI\Exception $e) {
        $this->fail((string) $e);
    }

    $json = json_decode($jsonapi);

    expect($json)
        ->toHaveProperty('data')
        ->toHaveProperty('included')
    ;
    expect($json->data)
        ->toMatchObject([
            'type' => 'pages',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'enabled' => true,
            'priority' => 1,
            'remark' => 'This is a test'
        ])
        ->toHaveProperty('contents')
    ;
    expect($json->data->attributes->contents)
        ->toBeArray()
        ->toHaveCount(1)
    ;
    expect($json->data->attributes->contents[0])
        ->toMatchObject([
            'locale' => 'nl',
            'title' => 'Test page',
            'summary' => 'This is a test',
            'html_summary' => '<p>This is a test</p>',
            'content' => 'This is a test',
            'html_content' => '<p>This is a test</p>'
        ])
    ;
    expect($json->included)
        ->toBeArray()
    ;
});

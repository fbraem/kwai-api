<?php
declare(strict_types=1);

use Kwai\Applications\Author\Actions\PageInputSchema;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Modules\Pages\UseCases\CreatePageCommand;
use Nette\Schema\ValidationException;

it('can process data', function () {
    $schema = new PageInputSchema(true);
    $data = [
        'data' => [
            'type' => 'pages',
            'attributes' => [
                'enabled' => true,
                'priority' => 1,
                'contents' => [
                    [
                        'title' => 'Test Page',
                        'locale' => 'nl',
                        'format' => 'md',
                        'summary' => 'Summary of the test page'
                    ]
                ]
            ]
        ]
    ];
    /* @var CreatePageCommand $result */
    $result = InputSchemaProcessor::create($schema)->process($data);
    assertInstanceOf(CreatePageCommand::class, $result);
    assertEquals($result->priority, 1);
    assertCount(1, $result->contents);
});

it('fails with invalid data', function () {
    $schema = new PageInputSchema(true);
    $data = [
        'data' => [
            'type' => 'pages',
            'attributes' => [
                'enabled' => true,
                'priority' => 'A',
                'contents' => [
                    [
                        'title' => 'Test Page',
                        'locale' => 'nl',
                        'format' => 'md',
                        'summary' => 'Summary of the test page'
                    ]
                ]
            ]
        ]
    ];
    InputSchemaProcessor::create($schema)->process($data);
})->expectException(ValidationException::class);

it('fails with missing data', function () {
    $schema = new PageInputSchema(true);
    $data = [
        'data' => [
            'type' => 'pages',
            'attributes' => [
                'enabled' => true,
                'priority' => 1,
                'contents' => [
                    [
                        'locale' => 'nl',
                        'format' => 'md',
                        'summary' => 'Summary of the test page'
                    ]
                ]
            ]
        ]
    ];
    InputSchemaProcessor::create($schema)->process($data);
})->expectException(ValidationException::class);

<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Modules\Pages\Presentation\REST\PageInputSchema;
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
    expect($result->priority)->toBe(1);
    expect($result->contents)->toBeArray()->toHaveCount(1);
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
})->throws(ValidationException::class);

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
})->throws(ValidationException::class);

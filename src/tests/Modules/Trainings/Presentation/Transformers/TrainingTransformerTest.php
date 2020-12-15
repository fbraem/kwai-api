<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Converter\MarkdownConverter;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Presentation\Transformers\TrainingTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

it('can transform a training', function () {
    $converterFactory = new ConverterFactory();
    $converterFactory->register('md', MarkdownConverter::class);

    $now = Timestamp::createNow();
    $training = new Training(
        event: new Event(
            startDate: $now,
            endDate: $now
        ),
        text: new Collection([
            new Text(
                new Locale('nl'),
                new DocumentFormat('md'),
                'Test Training',
                'This is a test training',
                null,
                new Creator(
                    1,
                    new Name('Jigoro', 'Kano')
                )
            )
        ])
    );
    $entity = new Entity(1, $training);
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer());
    $data = $fractal
        ->createData(TrainingTransformer::createForItem(
            $entity,
            $converterFactory
        ))
        ->toArray()
    ;
    expect($data)
        ->toBeArray()
        ->toHaveKey('data')
        ->and($data['data'])
        ->toBeArray()
        ->toMatchArray([
            'id' => '1',
            'updated_at' => null
        ])
        ->toHaveKey('event')
        ->and($data['data']['event'])
        ->toMatchArray([
            'start_date' => (string) $now,
            'end_date' => (string) $now,
            'time_zone' => 'UTC',
            'location' => null,
            'cancelled' => false,
            'active' => true
        ])
        ->toHaveKey('contents')
        ->and($data['data']['event']['contents'])
        ->toBeArray()
        ->toHaveCount(1)
        ->and($data['data']['event']['contents'][0])
        ->toMatchArray([
            'locale' => 'nl',
            'title' => 'Test Training'
        ])
    ;
});

<?php
declare(strict_types=1);

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
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\Team;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Presentation\Resources\TrainingResource;

it('can serialize a training to a JSON:API resource', function () {
    $training = new Entity(
        1,
        new Training(
            event: new Event(
                startDate: Timestamp::createFromString('2021-12-05 19:00:00'),
                endDate: Timestamp::createFromString('2021-12-05 21:00:00'),
                timezone: 'Europe/Brussels'
            ),
            text: collect([
                new Text(
                    locale: Locale::NL,
                    format: DocumentFormat::MARKDOWN,
                    title: 'Competition Training',
                    author: new Creator(
                        1,
                        new Name('Jigoro', 'Kano')
                    ),
                    summary: 'This is a training dedicated to competition'
                )
            ]),
            remark: 'This is a test training',
            coaches: collect([
                new TrainingCoach(
                    coach: new Entity(
                        1,
                        new Coach(
                            new Name('Jigoro', 'Kano')
                        )
                    ),
                    creator: new Creator(
                        1,
                        new Name('Jigoro', 'Kano')
                    ),
                    head: true
                )
            ]),
            teams: collect([
                new Entity(
                    1,
                    new Team('U15')
                )
            ])
        )
    );

    $converter = new ConverterFactory();
    $converter->register('md', MarkdownConverter::class);
    $resource = new TrainingResource(
        $training,
        $converter
    );

    try {
        $jsonapi = JSONAPI\Document::createFromObject($resource)->serialize();
    } catch (JSONAPI\Exception $e) {
        $this->fail((string) $e);
    }

    $json = json_decode($jsonapi);

    expect($json)
        ->toHaveProperty('data')
    ;
    expect($json->data)
        ->toMatchObject([
            'type' => 'trainings',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
        ->toHaveProperty('relationships')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'remark' => 'This is a test training',
            'event' => (object)[
                'start_date' => '2021-12-05 19:00:00',
                'end_date' => '2021-12-05 21:00:00',
                'timezone' => 'Europe/Brussels',
                'cancelled' => false,
                'active' => true,
                'location' => null
            ],
            'contents' => [
                (object) [
                    'locale' => 'nl',
                    'title' => 'Competition Training',
                    'summary' => 'This is a training dedicated to competition',
                    'html_summary' => '<p>This is a training dedicated to competition</p>',
                    'content' => null,
                    'html_content' => ''
                ]
            ]
        ])
    ;
    expect($json->data->relationships)
        ->toHaveProperty('coaches')
        ->toHaveProperty('teams')
    ;

    expect($json)
        ->toHaveProperty('included')
    ;
});

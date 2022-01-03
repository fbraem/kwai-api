<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Core\Infrastructure\Presentation\TextInputSchema;
use Kwai\Modules\Trainings\UseCases\CreateTrainingCommand;
use Kwai\Modules\Trainings\UseCases\UpdateTrainingCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class TrainingSchema
 */
class TrainingSchema implements InputSchema
{
    private TextInputSchema $textSchema;

    public function __construct(
        private bool $create = false
    ) {
        $this->textSchema = new TextInputSchema();
    }

    /**
     * @inheritDoc
     */
    public function create(): Structure
    {
        return Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::anyOf('trainings'),
                'id' => Expect::string()->required(!$this->create),
                'attributes' => Expect::structure([
                    'contents' => Expect::arrayOf(
                        $this->textSchema->create()
                    )->required($this->create),
                    'event' => Expect::structure([
                        'location' => Expect::string(),
                        'start_date' => Expect::string()->required(),
                        'end_date' => Expect::string()->required(),
                        'timezone' => Expect::string()->required(),
                        'cancelled' => Expect::bool()->default(false),
                        'active' => Expect::bool()->default(true)
                    ])->required(),
                    'coaches' => Expect::arrayOf(
                        Expect::structure([
                            'id' => Expect::string()->castTo('int')->required(),
                            'head' => Expect::bool()->required(),
                            'present' => Expect::bool()->required(),
                            'payed' => Expect::bool()->required()
                        ])
                    )->required(false),
                    'remark' => Expect::string()->nullable()
                ]),
                'relationships' => Expect::structure([
                    'definition' => Expect::structure([
                        'data' => Expect::structure([
                            'type' => Expect::anyOf('definitions'),
                            'id' => Expect::string()->required()
                        ])
                    ])->required(false),
                    'coaches' => Expect::structure([
                        'data' => Expect::arrayOf(Expect::structure([
                            'type' => Expect::anyOf('coaches'),
                            'id' => Expect::string()->required()
                        ]))
                    ])->required(false),
                    'teams' => Expect::structure([
                        'data' => Expect::arrayOf(Expect::structure([
                            'type' => Expect::anyOf('teams'),
                            'id' => Expect::string()->required()
                        ]))
                    ])->required(false)
                ])
            ])
        ]);
    }

    /**
     * @inheritDoc
     */
    public function process($normalized): UpdateTrainingCommand|CreateTrainingCommand
    {
        if ($this->create) {
            $command = new CreateTrainingCommand();
        } else {
            $command = new UpdateTrainingCommand();
            $command->id = (int) $normalized->data->id;
        }

        $command->contents = $normalized->data->attributes->contents;

        $command->start_date = $normalized->data->attributes->event->start_date;
        $command->end_date = $normalized->data->attributes->event->end_date;
        $command->timezone = $normalized->data->attributes->event->timezone;
        $command->location = $normalized->data->attributes->event->location;
        $command->cancelled = $normalized->data->attributes->event->cancelled;
        $command->active = $normalized->data->attributes->event->active;

        $command->remark = $normalized->data->attributes->remark;
        $command->coaches = $normalized->data->attributes->coaches;

        if ($normalized->data->relationships?->teams?->data) {
            $command->teams = array_map(
                fn ($team) => (int) $team->id,
                $normalized->data->relationships->teams->data
            );
        }
        if ($normalized->data->relationships?->definition?->data) {
            $command->definition = (int) $normalized->data->relationships->definition->data->id;
        }

        return $command;
    }
}

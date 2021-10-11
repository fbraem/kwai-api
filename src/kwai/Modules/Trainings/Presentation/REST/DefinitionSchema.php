<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\REST;

use Kwai\Modules\Trainings\UseCases\CreateDefinitionCommand;
use Kwai\Modules\Trainings\UseCases\UpdateDefinitionCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class DefinitionSchema
 */
class DefinitionSchema implements \Kwai\Core\Infrastructure\Presentation\InputSchema
{
    public function __construct(
        private bool $create = false
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(): Structure
    {
        return Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::anyOf('definitions'),
                'id' => Expect::string()->required(!$this->create),
                'attributes' => Expect::structure([
                    'name' => Expect::string(),
                    'description' => Expect::string(),
                    'weekday' => Expect::int(),
                    'start_time' => Expect::string(),
                    'end_time' => Expect::string(),
                    'time_zone' => Expect::string(),
                    'active' => Expect::bool(false),
                    'location' => Expect::string()->nullable(),
                    'remark' => Expect::string()->nullable(),
                ]),
                'relationships' => Expect::structure([
                    'team' => Expect::structure([
                        'data' => Expect::structure([
                            'type' => Expect::anyOf('teams'),
                            'id' => Expect::string()->required()
                        ])
                    ])->required(false)
                ])
            ])
        ]);
    }

    /**
     * @inheritDoc
     */
    public function process($normalized): UpdateDefinitionCommand|CreateDefinitionCommand
    {
        if ($this->create) {
            $command = new CreateDefinitionCommand();
        } else {
            $command = new UpdateDefinitionCommand();
            $command->id = (int) $normalized->data->id;
        }

        $command->name = $normalized->data->attributes->name;
        $command->description = $normalized->data->attributes->description;
        $command->weekday = $normalized->data->attributes->weekday;
        $command->start_time = $normalized->data->attributes->start_time;
        $command->end_time = $normalized->data->attributes->end_time;
        $command->time_zone = $normalized->data->attributes->time_zone;
        $command->active = $normalized->data->attributes->active;
        $command->location = $normalized->data->attributes->location;
        $command->remark = $normalized->data->attributes->remark;

        return $command;
    }
}

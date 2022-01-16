<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Modules\Users\UseCases\UpdateUserCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class UserSchema
 *
 * Inputschema for creating or updating a user.
 */
class UserSchema implements InputSchema
{
    /**
     * Constructor.
     *
     * @param bool $create Is this schema used to create a user?
     */
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
                'type' => Expect::anyOf('users'),
                'id' => Expect::string()->required(!$this->create),
                'attributes' => Expect::structure([
                    'email' => Expect::string()->required($this->create),
                    'first_name' => Expect::string()->required($this->create),
                    'last_name' => Expect::string()->required($this->create),
                    'remark' => Expect::string()->nullable(),
                ]),
                'relationships' => Expect::structure([
                    'abilities' => Expect::structure([
                        'data' => Expect::arrayOf(Expect::structure([
                            'type' => Expect::anyOf('abilities'),
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
    public function process($normalized): UpdateUserCommand
    {
        $command = new UpdateUserCommand();

        $command->email = $normalized->data->attributes->email;
        $command->first_name = $normalized->data->attributes->first_name;
        $command->last_name = $normalized->data->attributes->last_name;
        $command->uuid = $normalized->data->id;
        $command->remark = $normalized->data->attributes->remark;

        if ($normalized->data->relationships?->abilities?->data) {
            $command->abilities = array_map(
                fn ($ability) => (int) $ability->id,
                $normalized->data->relationships->abilities->data
            );
        }

        return $command;
    }
}

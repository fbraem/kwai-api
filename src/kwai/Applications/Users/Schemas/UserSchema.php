<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Schemas;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Modules\Users\UseCases\UpdateUserCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class UserSchema
 *
 * Input schema for creating or updating a user.
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
                    'admin' => Expect::bool()->default(false)
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
        $command->admin = $normalized->data->attributes->admin;

        return $command;
    }
}

<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Schemas;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
use Kwai\Modules\Users\UseCases\UpdateUserInvitationCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class UserInvitationSchema
 */
class UserInvitationSchema implements InputSchema
{
    public function __construct(private bool $create = false)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(): Structure
    {
        if ($this->create) {
            $attributes = [
                'email' => Expect::string()->required(),
                'name' => Expect::string()->required(),
                'expiration' => Expect::int(),
            ];
        } else {
            $attributes = [
                'renew' => Expect::bool()->required(false),
            ];
        }
        $attributes['remark'] = Expect::string();

        return Expect::Structure([
            'data' => Expect::structure([
                'type' => Expect::string(),
                'id' => Expect::string()->required(!$this->create),
                'attributes' => Expect::structure($attributes)
            ])
        ]);
    }

    /**
     * @inheritDoc
     */
    public function process($normalized): InviteUserCommand|UpdateUserInvitationCommand
    {
        if ($this->create) {
            $command = new InviteUserCommand();
            $command->email = $normalized->data->attributes->email;
            $command->name = $normalized->data->attributes->name;
        } else {
            $command = new UpdateUserInvitationCommand();
            $command->uuid = $normalized->data->id;
            $command->renew = $normalized->data->attributes->renew;
        }
        $command->remark = $normalized->data->attributes->remark;
        return $command;
    }
}

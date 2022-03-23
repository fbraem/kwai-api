<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Schemas;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
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
        return Expect::Structure([
            'data' => Expect::structure([
                'type' => Expect::string(),
                'attributes' => Expect::structure([
                    'email' => Expect::string()->required($this->create),
                    'name' => Expect::string()->required($this->create),
                    'remark' => Expect::string(),
                    'expiration' => Expect::int()
                ])
            ])
        ]);
    }

    /**
     * @inheritDoc
     */
    public function process($normalized): InviteUserCommand
    {
        $command = new InviteUserCommand();
        $command->email = $normalized->data->attributes->email;
        $command->name = $normalized->data->attributes->name;
        $command->remark = $normalized->data->attributes->remark;
        return $command;
    }
}

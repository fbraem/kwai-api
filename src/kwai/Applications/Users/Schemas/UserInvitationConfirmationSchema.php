<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Schemas;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Modules\Users\UseCases\ConfirmInvitationCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class UserInvitationSchema
 */
class UserInvitationConfirmationSchema implements InputSchema
{
    public function __construct()
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
                "id" => Expect::string(),
                'attributes' => Expect::structure([
                    'email' => Expect::string()->required(),
                    'first_name' => Expect::string()->required(),
                    'last_name' =>  Expect::string()->required(),
                    'password' => Expect::string()->required(),
                    'remark' => Expect::string()
                ])
            ])
        ]);
    }

    /**
     * @inheritDoc
     */
    public function process($normalized): ConfirmInvitationCommand
    {
        $command = new ConfirmInvitationCommand();
        $command->uuid = $normalized->data->id;
        $command->email = $normalized->data->attributes->email;
        $command->firstName = $normalized->data->attributes->first_name;
        $command->lastName = $normalized->data->attributes->last_name;
        $command->remark = $normalized->data->attributes->remark;
        $command->password = $normalized->data->attributes->password;
        return $command;
    }
}

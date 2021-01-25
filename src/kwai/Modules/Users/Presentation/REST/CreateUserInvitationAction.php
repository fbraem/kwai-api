<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserInvitationTransformer;
use Kwai\Modules\Users\UseCases\InviteUser;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CreateUserInvitationAction
 *
 * Action to create a new user invitation.
 */
class CreateUserInvitationAction extends Action
{
    const EXPIRE_IN_DAYS = 15;

    /**
     * Create a command from the request data
     * @param array $data
     * @return InviteUserCommand
     * @throws ValidationException
     */
    private function createCommand(array $data): InviteUserCommand
    {
        $schema = Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::string(),
                'attributes' => Expect::structure([
                    'email' => Expect::string()->required(),
                    'name' => Expect::string()->required(),
                    'remark' => Expect::string()
                ])
            ])
        ]);
        $processor = new Processor();
        $normalized = $processor->process($schema, $data);

        $command = new InviteUserCommand();
        $command->email = $normalized->data->attributes->email;
        $command->name = $normalized->data->attributes->name;
        $command->remark = $normalized->data->attributes->remark ?? null;
        return $command;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param          $args
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('kwai.user');
        try {
            $command = $this->createCommand($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        // Add some additional properties to the command.
        $command->expiration = self::EXPIRE_IN_DAYS;
        $from = $this->getContainerEntry('settings')['mail']['from'];
        if (is_array($from)) {
            $command->sender_mail = (string) array_key_first($from);
            $command->sender_name = $from[$command->sender_mail];
        } else {
            $command->sender_mail = $from;
            $command->sender_name = '';
        }

        //TODO: for now, we pass the user as argument to the use case.
        //TODO: In the future, move this to an execution context?
        //TODO: Check if we need to create a InviteService?
        try {
            $database = $this->getContainerEntry('pdo_db');
            $invitation = (new InviteUser(
                new UserInvitationDatabaseRepository($database),
                new UserAccountDatabaseRepository($database),
                new MailDatabaseRepository($database),
                new MailTemplate(
                    'User Invitation',
                    $this->getContainerEntry('template')->createTemplate('User/invitation_html'),
                    $this->getContainerEntry('template')->createTemplate('User/invitation_txt'),
                ),
                $user
            ))($command);
        } catch (UnprocessableException $e) {
            return (new SimpleResponse(
                422,
                $e->getMessage()
            ))($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        return (new ResourceResponse(
            UserInvitationTransformer::createForItem(
                $invitation
            )
        ))($response);
    }
}

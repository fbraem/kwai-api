<?php
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\ResourceResponse;
use Core\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\AlreadyExistException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Mails\Infrastructure\Repositories\RecipientDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserInvitationTransformer;
use Kwai\Modules\Users\UseCases\InviteUser;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateUserInvitationAction
{
    /**
     * The container
     */
    private ContainerInterface $container;

    /**
     * CreateUserInvitationAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Creates the command input from the JSONAPI payload.
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
     * @param Request $request
     * @param Response $response
     * @param $args
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
        $command->expiration = 14;
        $from = $this->container->get('settings')['mail']['from'];
        if (is_array($from)) {
            $command->sender_mail = array_key_first($from);
            $command->sender_name = $from[$command->sender_mail];
        } else {
            $command->sender_mail = $from;
            $command->sender_name = '';
        }

        //TODO: for now, we pass the user as argument to the use case.
        //TODO: In the future, move this to an execution context?
        //TODO: Check if we need to create a InviteService?
        try {
            $invitation = (new InviteUser(
                new UserInvitationDatabaseRepository(
                    $this->container->get('pdo_db')
                ),
                new UserDatabaseRepository(
                    $this->container->get('pdo_db')
                ),
                new MailDatabaseRepository(
                    $this->container->get('pdo_db')
                ),
                new RecipientDatabaseRepository(
                    $this->container->get('pdo_db')
                ),
                new MailTemplate(
                    'User Invitation',
                    $this->container->get('template')->createTemplate('User/invitation_html'),
                    $this->container->get('template')->createTemplate('User/invitation_txt'),
                ),
                $user
            ))($command);
        } catch (AlreadyExistException $e) {
            return (new SimpleResponse(
                422,
                'The email address is already used'
            ))($response);
        }

        return (new ResourceResponse(
            UserInvitationTransformer::createForItem(
                $invitation
            )
        ))($response);
    }
}

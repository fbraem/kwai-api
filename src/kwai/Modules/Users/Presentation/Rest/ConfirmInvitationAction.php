<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\NotFoundResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\ExpiredException;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserAccountTransformer;
use Kwai\Modules\Users\UseCases\ConfirmInvitation;
use Kwai\Modules\Users\UseCases\ConfirmInvitationCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * ConfirmInvitationAction
 */
class ConfirmInvitationAction extends Action
{
    /**
     * Create a command from the request data
     * @param array $data
     * @return ConfirmInvitationCommand
     */
    private function createCommand(array $data): ConfirmInvitationCommand
    {
        $schema = Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::string(),
                'attributes' => Expect::structure([
                    'remark' => Expect::string(),
                    'first_name' => Expect::string()->required(),
                    'last_name' => Expect::string()->required(),
                    'password' => Expect::string()->required()
                ])
            ])
        ]);

        $processor = new Processor();
        $normalized = $processor->process($schema, $data);

        $command = new ConfirmInvitationCommand();
        $command->firstName = $normalized->data->attributes->first_name;
        $command->lastName = $normalized->data->attributes->last_name;
        $command->remark = $normalized->data->attributes->remark ?? null;
        $command->password = $normalized->data->attributes->password;

        return $command;
    }

    /**
     * Confirm the invitation and create a user
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        try {
            $command = $this->createCommand($request->getParsedBody());
            $command->uuid = $args['uuid'];
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        try {
            $database = $this->getContainerEntry('pdo_db');
            $userAccount = (new ConfirmInvitation(
                new UserInvitationDatabaseRepository($database),
                new UserDatabaseRepository($database)
            ))($command);
        } catch (ExpiredException $e) {
            return (new SimpleResponse(422, 'Invitation is expired'))($response);
        } catch (NotFoundException $e) {
            return (new NotFoundResponse('User invitation does not exist'))($response);
        }

        return (new ResourceResponse(
            UserAccountTransformer::createForItem($userAccount)
        ))($response);
    }
}

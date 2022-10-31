<?php
/**
 * @package Applications
 * @subpackage Auth
 */
declare(strict_types=1);

namespace Kwai\Applications\Auth\Actions;

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\OkResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\UseCases\ChangePassword;
use Kwai\Modules\Users\UseCases\ChangePasswordCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResetPasswordAction
 *
 * Action to change the password of the user
 */
#[Route(
    path: '/auth/change',
    name: 'auth.change',
    options: ['auth' => true],
    methods: ['POST']
)]
final class ChangePasswordAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    private function createCommand(array $data): ChangePasswordCommand
    {
        $schema = Expect::structure([
            'password' => Expect::string()->required()
        ]);

        $processor = new Processor();
        $normalized = $processor->process($schema, $data);

        $command = new ChangePasswordCommand();
        $command->password = $normalized->password;

        return $command;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $command = $this->createCommand($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        try {
            ChangePassword::create(
                new UserAccountDatabaseRepository($this->database)
            )($command, $request->getAttribute('kwai.user'));
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (NotAllowedException $e) {
            return (
                new SimpleResponse(403, 'Password change is not allowed.')
            )($response);
        } catch (UserAccountNotFoundException $e) {
            return (
                new SimpleResponse(400, 'User account could not be found.')
            )($response);
        }
        return (new OkResponse())($response);
    }
}
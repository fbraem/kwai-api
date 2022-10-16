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
use Kwai\Modules\Users\Domain\Exceptions\UserRecoveryExpiredException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserRecoveryDatabaseRepository;
use Kwai\Modules\Users\UseCases\ResetPassword;
use Kwai\Modules\Users\UseCases\ResetPasswordCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResetPasswordAction
 *
 * Action to reset a password of the user
 */
#[Route(
    path: '/auth/reset',
    name: 'auth.reset',
    methods: ['POST']
)]
class ResetPasswordAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    private function createCommand(array $data): ResetPasswordCommand
    {
        $schema = Expect::structure([
            'uuid' => Expect::string()->required(),
            'password' => Expect::string()->required()
        ]);

        $processor = new Processor();
        $normalized = $processor->process($schema, $data);

        $command = new ResetPasswordCommand();
        $command->uuid = $normalized->uuid;
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
            ResetPassword::create(
                new UserRecoveryDatabaseRepository($this->database),
                new UserAccountDatabaseRepository($this->database)
            )($command);
        } catch (UserRecoveryExpiredException) {
            return (new SimpleResponse(400, 'Code is expired'))($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserAccountNotFoundException|NotAllowedException) {
            return (new SimpleResponse(400, 'Bad request'))($response);
        }

        return (new OkResponse())($response);
    }
}
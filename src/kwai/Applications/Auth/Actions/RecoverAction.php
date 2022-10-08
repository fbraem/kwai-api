<?php
/**
 * @package Applications
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Applications\Auth\Actions;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\MailerDependency;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\OkResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserRecoveryDatabaseRepository;
use Kwai\Modules\Users\UseCases\RecoverUser;
use Kwai\Modules\Users\UseCases\RecoverUserCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RecoverAction
 *
 * Action to email a user to change his/her password.
 */
#[Route(
    path: '/auth/recover',
    name: 'auth.recover',
    methods: ['POST']
)]
final class RecoverAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        private ?PlatesEngine $templateEngine = null,
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->templateEngine ??= depends('kwai.template_engine', TemplateDependency::class);
    }

    private function createCommand(array $data): RecoverUserCommand
    {
        $schema = Expect::structure([
            'username' => Expect::string()->required()
        ]);
        $processor = new Processor();
        $normalized = $processor->process($schema, $data);

        $command = new RecoverUserCommand();
        $command->email = $normalized->username;

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
            RecoverUser::create(
                new UserRecoveryDatabaseRepository($this->database),
                new UserAccountDatabaseRepository($this->database),
                new MailDatabaseRepository($this->database),
                depends('kwai.mailer', MailerDependency::class),
                new MailTemplate(
                    'Recover user password',
                    $engine->createTemplate('Users::recover_html'),
                    $engine->createTemplate('Users::recover_txt')
                )

            )($command);
        } catch (UserAccountNotFoundException) {
            // ignore
        }

        // Don't answer with 404, because that way a stranger knows that the email
        // is not known in our system.
        return (new OkResponse())($response);
    }
}
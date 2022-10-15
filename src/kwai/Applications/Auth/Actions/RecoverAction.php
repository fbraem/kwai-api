<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Auth\Actions;

use Kwai\Applications\Auth\Resources\UserRecoveryResource;
use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\MailerDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Core\Infrastructure\Mailer\MailerService;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\OkResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\TemplateEngine;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserRecoveryDatabaseRepository;
use Kwai\Modules\Users\UseCases\RecoverUser;
use Kwai\Modules\Users\UseCases\RecoverUserCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreateUserRecoveryAction
 *
 * Action to create a new user recovery
 */
#[Route(
    path: '/auth/recover',
    name: 'auth.recover',
    methods: ['POST']
)]
class RecoverAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        private ?TemplateEngine $templateEngine = null,
        private ?Configuration $settings = null,
        private ?MailerService $mailerService = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($logger);
        $this->settings ??= depends('kwai.settings', Settings::class);
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->mailerService ??= depends('kwai.mailer', MailerDependency::class);
        $this->templateEngine ??= depends('kwai.template_engine', TemplateDependency::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = new RecoverUserCommand();

        $form = $request->getParsedBody();
        if (is_array($form) && isset($form['email'])) {
            $command->email = $form['email'];
        } else {
            return (new SimpleResponse(
                400,
                'Email address is missing'
            ))($response);
        }

        $recovery = null;
        try {
            $recovery = RecoverUser::create(
                new UserRecoveryDatabaseRepository($this->database),
                new UserAccountDatabaseRepository($this->database),
                $this->mailerService,
                new MailTemplate(
                    $this->templateEngine->createTemplate('Users::recover_html'),
                    $this->templateEngine->createTemplate('Users::recover_txt')
                )
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserAccountNotFoundException $e) {
            $this->logException($e);
        }

        if ($recovery) {
            $resource = new UserRecoveryResource($recovery);
            return (new JSONAPIResponse(
                JSONAPI\Document::createFromObject($resource)
            ))($response);
        }

        return (new OkResponse())($response);
    }
}
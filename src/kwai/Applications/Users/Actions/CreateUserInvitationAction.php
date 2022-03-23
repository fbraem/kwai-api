<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Users\Resources\UserInvitationResource;
use Kwai\Applications\Users\Schemas\UserInvitationSchema;
use Kwai\Applications\Users\Security\InviterPolicy;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\ForbiddenResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\PlatesEngine;
use Kwai\JSONAPI;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\InviteUser;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class CreateUserInvitationAction
 *
 * Action to create a new user invitation.
 */
#[Route(
  path: '/users/invitations',
  name: 'users.invitations.create',
  options: ['auth' => true],
  methods: ['POST']
)]
class CreateUserInvitationAction extends Action
{
    const EXPIRE_IN_DAYS = 15;

    public function __construct(
        private ?Connection $database = null,
        private ?PlatesEngine $templateEngine = null,
        private ?Configuration $settings = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($logger);
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->templateEngine ??= depends('kwai.template_engine', TemplateDependency::class);
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('kwai.user');
        $policy = new InviterPolicy($user);
        if (!$policy->canCreate()) {
            return (new ForbiddenResponse('Not allowed to invite a user'))($response);
        }

        try {
            $command = InputSchemaProcessor::create(new UserInvitationSchema(true))
                ->process($request->getParsedBody())
            ;
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        // Add some additional properties to the command.
        $command->expiration = self::EXPIRE_IN_DAYS;
        $from = $this->settings->getMailerConfiguration()->getFromAddress();
        $command->sender_mail = (string) $from->getEmail();
        $command->sender_name = $from->getName();

        $creator = new Creator($user->id(), $user->getUsername());

        try {
            $invitation = (new InviteUser(
                new UserInvitationDatabaseRepository($this->database),
                new UserAccountDatabaseRepository($this->database),
                new MailDatabaseRepository($this->database),
                new MailTemplate(
                    'User Invitation',
                    $this->templateEngine->createTemplate('User/invitation_html'),
                    $this->templateEngine->createTemplate('User/invitation_txt'),
                ),
                $creator
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

        $resource = new UserInvitationResource($invitation);
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}

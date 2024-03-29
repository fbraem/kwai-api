<?php
/**
 * @package Applications
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Applications\Auth\Actions;

use Firebase\JWT\JWT;
use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Configuration\SecurityConfiguration;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotAuthorizedResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Security\JsonWebToken;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class LoginAction
 *
 * Action to log in a user with email/pwd and return access- and refreshtoken on success.
 */
#[Route(
    path: '/auth/login',
    name: 'auth.login',
    methods: ['POST'],
)]
class LoginAction extends Action
{
    private SecurityConfiguration $configuration;

    public function __construct(
        private ?Connection $database = null,
        private ?Configuration $settings = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->settings ??= depends('kwai.settings', Settings::class);
        $this->configuration = $this->settings->getSecurityConfiguration();
    }

    /**
     * Create a command from the request data
     * @param array $data
     * @return AuthenticateUserCommand
     */
    private function createCommand(array $data): AuthenticateUserCommand
    {
        $schema = Expect::structure([
            'username' => Expect::string()->required(),
            'password' => Expect::string()->required(),
        ])->otherItems(Expect::string());
        $processor = new Processor();
        $normalized = $processor->process($schema, $data);

        $command = new AuthenticateUserCommand();
        $command->email = $normalized->username;
        $command->password = $normalized->password;

        return $command;
    }

    /**
     * Login the user and return an access- and refreshtoken.
     * @param  Request  $request  The current HTTP request
     * @param  Response $response The current HTTP response
     * @param  array $args        Route’s named placeholders
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args
    ): Response {
        try {
            $command = $this->createCommand($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        try {
            $refreshToken = AuthenticateUser::create(
                new UserAccountDatabaseRepository($this->database),
                new AccessTokenDatabaseRepository($this->database),
                new RefreshTokenDatabaseRepository($this->database)
            )($command);
        } catch (AuthenticationException) {
            return (new NotAuthorizedResponse('Authentication failed'))($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserAccountNotFoundException) {
            return (new NotAuthorizedResponse('Unknown user'))($response);
        }

        $secret = $this->configuration->getSecret();
        $algorithm = $this->configuration->getAlgorithm();

        $accessToken = $refreshToken->getAccessToken();
        $data = [
            'access_token' => (new JsonWebToken(
                $secret,
                $algorithm,
                (object)[
                    'iat' => $accessToken->getTraceableTime()->getCreatedAt()->format('U'),
                    'exp' => $accessToken->getExpiration()->format('U'),
                    'jti' => strval($accessToken->getIdentifier()),
                    'sub' => strval($accessToken->getUserAccount()->getUser()->getUuid()),
                    'scope' => []
                ],
            ))->encode(),
            'refresh_token' => (new JsonWebToken(
                $secret,
                $algorithm,
                (object) [
                    'iat' => $refreshToken->getTraceableTime()->getCreatedAt()->format('U'),
                    'exp' => $refreshToken->getExpiration()->format('U'),
                    'jti' => strval($refreshToken->getIdentifier())
                ],
            ))->encode(),
            'expires' => strval($accessToken->getExpiration())
        ];

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $response
            ->withStatus(201)
            ->withHeader("Content-Type", "application/json")
        ;
    }
}

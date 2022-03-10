<?php
/**
 * @package Applications
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Applications\Auth\Actions;

use Exception;
use Firebase\JWT\ExpiredException;
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
use Kwai\Modules\Users\Domain\Exceptions\RefreshTokenNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\CreateRefreshToken;
use Kwai\Modules\Users\UseCases\CreateRefreshTokenCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class RefreshTokenAction
 *
 * Action to create a new refresh- and accesstoken (when the refreshtoken is valid).
 */
#[Route(
    path: '/auth/access_token',
    name: 'auth.access_token',
    methods: ['POST'],
)]
class RefreshTokenAction extends Action
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
     * @param  Request  $request  The current HTTP request
     * @param  Response $response The current HTTP response
     * @param  array    $args     Route’s named placeholders
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $data = $request->getParsedBody();

        $secret = $this->configuration->getSecret();
        $algorithm = $this->configuration->getAlgorithm();

        if (!isset($data['refresh_token'])) {
            return (new SimpleResponse(400, 'No refresh token passed'))($response);
        }

        try {
            $decodedRefreshToken = JsonWebToken::decode(
                $data['refresh_token'],
                $secret,
                $algorithm
            )->getObject();
        } catch (ExpiredException) {
            return (new NotAuthorizedResponse('Refreshtoken expired'))($response);
        } catch (Exception $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'An exception occurred while decoding the refreshtoken.')
            )($response);
        }

        $command = new CreateRefreshTokenCommand();
        $command->identifier = $decodedRefreshToken->jti;

        try {
            $refreshToken = CreateRefreshToken::create(
                new RefreshTokenDatabaseRepository($this->database),
                new AccessTokenDatabaseRepository($this->database)
            )($command);
        } catch (AuthenticationException) {
            return (new NotAuthorizedResponse('Authentication failed'))($response);
        } catch (RepositoryException) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (RefreshTokenNotFoundException) {
            return (new NotAuthorizedResponse('Unknown refreshtoken'))($response);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $accessToken = $refreshToken->getAccessToken();
        /** @noinspection PhpUndefinedMethodInspection */
        $data = [
            'access_token' => (new JsonWebToken(
                $secret,
                $algorithm,
                (object) [
                    'iat' => $accessToken->getTraceableTime()->getCreatedAt()->format('U'),
                    'exp' => $accessToken->getExpiration()->format('U'),
                    'jti' => strval($accessToken->getIdentifier()),
                    'sub' => strval($accessToken->getUserAccount()->getUser()->getUuid()),
                    'scope' => []
                ]
                ))->encode(),
            'refresh_token' => (new JsonWebToken(
                $secret,
                $algorithm,
                (object)[
                    'iat' => $refreshToken->getTraceableTime()->getCreatedAt()->format('U'),
                    'exp' => $refreshToken->getExpiration()->format('U'),
                    'jti' => strval($refreshToken->getIdentifier())
                ],
            ))->encode(),
            'expires' => strval($accessToken->getExpiration())
        ];
        $response
            ->getBody()
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
        ;
        return $response
            ->withStatus(201)
            ->withHeader("Content-Type", "application/json")
        ;
    }
}

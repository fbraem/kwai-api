<?php
/**
 * @package Kwai/Modules
 * @subpackage User
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Core\Responses\NotAuthorizedResponse;

use Firebase\JWT\JWT;

/**
 * Login a user with email/pwd and return access- and refreshtoken on succes.
 */
class LoginAction
{
    /**
     * The DI container
     */
    private ContainerInterface $container;

    /**
     * Constructor
     * @param ContainerInterface $container The DI container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Login the user and return an access- and refreshtoken.
     * @param  Request  $request  The current HTTP request
     * @param  Response $response The current HTTP response
     * @param  string[] $args     Route’s named placeholders
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $data = $request->getParsedBody();
        $command = new AuthenticateUserCommand([
            'email' => $data['username'],
            'password' => $data['password']
        ]);

        try {
            $refreshToken = (new AuthenticateUser(
                new UserDatabaseRepository($this->container->get('pdo_db')),
                new AccessTokenDatabaseRepository($this->container->get('pdo_db')),
                new RefreshTokenDatabaseRepository($this->container->get('pdo_db'))
            ))($command);
        } catch (NotFoundException $nfe) {
            return (new NotAuthorizedResponse('Unknown user'))($response);
        } catch (AuthenticationException $ae) {
            return (new NotAuthorizedResponse('Authentication failed'))($response);
        }

        $secret = $this->container->get('settings')['security']['secret'];
        $algorithm = $this->container->get('settings')['security']['algorithm'];

        /** @noinspection PhpUndefinedMethodInspection */
        $accessToken = $refreshToken->getAccessToken();
        /** @noinspection PhpUndefinedMethodInspection */
        $data = [
            'access_token' => JWT::encode(
                [
                    'iat' => $accessToken->getTraceableTime()->getCreatedAt()->format('U'),
                    'exp' => $accessToken->getExpiration()->format('U'),
                    'jti' => strval($accessToken->getIdentifier()),
                    'sub' => strval($accessToken->getUserAccount()->getUser()->getUuid()),
                    'scope' => []
                ],
                $secret,
                $algorithm
            ),
            'refresh_token' => JWT::encode(
                [
                    'iat' => $refreshToken->getTraceableTime()->getCreatedAt()->format('U'),
                    'exp' => $refreshToken->getExpiration()->format('U'),
                    'jti' => strval($refreshToken->getIdentifier())
                ],
                $secret,
                $algorithm
            ),
            'expires' => strval($accessToken->getExpiration())
        ];
        $response->withStatus(201)
            ->withHeader("Content-Type", "application/json")
            ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $response;
    }
}

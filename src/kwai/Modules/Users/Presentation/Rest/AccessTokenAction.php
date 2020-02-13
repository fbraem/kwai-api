<?php

namespace Kwai\Modules\Users\Presentation\Rest;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Core\Responses\NotAuthorizedResponse;

use Firebase\JWT\JWT;

class AccessTokenAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $command = new AuthenticateUserCommand([
            'email' => $data['username'],
            'password' => $data['password']
        ]);

        try {
            $refreshToken = (new AuthenticateUser(
                new UserDatabaseRepository($this->container->get('pdo_db')),
                new AccessTokenDatabaseRepository($this->container->get('pdo_db'))
            ))($command);
        } catch (NotFoundException $nfe) {
            return new NotAuthorizedResponse('Unknown user');
        } catch (AuthenticationException $ae) {
            return new NotAuthorizedResponse('Authentication failed');
        }

        $secret = $this->container->get('settings')['oauth2']['client']['secret'];
        $accessToken = $refreshToken->getAccessToken();
        $data = [
            'access_token' => JWT::encode(
                [
                    'iat' => $accessToken->getTraceableTime()->getCreatedAt()->format('U'),
                    'exp' => $accessToken->getExpiration()->format('U'),
                    'jti' => strval($accessToken->getIdentifier()),
                    'sub' => strval($accessToken->getUser()->getUuid()),
                    'scope' => []
                ],
                $secret,
                "HS256"
            ),
            'refresh_token' => JWT::encode(
                [
                    'iat' => $refreshToken->getTraceableTime()->getCreatedAt()->format('U'),
                    'exp' => $refreshToken->getExpiration()->format('U'),
                    'jti' => strval($refreshToken->getIdentifier())
                ],
                $secret,
                "HS256"
            ),
            'expires' => strval($accessToken->getExpiration())
        ];
        $response->withStatus(201)
            ->withHeader("Content-Type", "application/json")
            ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $response;
    }
}

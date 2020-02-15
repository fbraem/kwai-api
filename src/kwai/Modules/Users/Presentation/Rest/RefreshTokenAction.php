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

use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Kwai\Modules\Users\UseCases\CreateRefreshToken;
use Kwai\Modules\Users\UseCases\CreateRefreshTokenCommand;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Core\Responses\NotAuthorizedResponse;

use Firebase\JWT\JWT;

/**
 * Return a new refresh- and accesstoken (when the refreshtoken is valid).
 */
class RefreshTokenAction
{
    /**
     * The DI container
     * @var ContainerInterface
     */
    private $container;

    /**
     * The constructor
     * @param ContainerInterface $container The DI container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Create a new accesstoken
     * @param  Request  $request  The current HTTP request
     * @param  Response $response The current HTTP response
     * @param  array    $args     Route’s named placeholders
     * @return Response
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $data = $request->getParsedBody();

        $secret = $this->container->get('settings')['oauth2']['client']['secret'];
        $decodedRefreshToken = JWT::decode(
            $data['refresh_token'],
            $secret,
            ['HS256']
        );

        $command = new CreateRefreshTokenCommand([
            'refresh_token_identifier' => $decodedRefreshToken->jti
        ]);

        try {
            $refreshToken = (new CreateRefreshToken(
                new RefreshTokenDatabaseRepository($this->container->get('pdo_db')),
                new AccessTokenDatabaseRepository($this->container->get('pdo_db'))
            ))($command);
        } catch (NotFoundException $nfe) {
            return new NotAuthorizedResponse('Unknown refreshtoken');
        } catch (AuthenticationException $ae) {
            return new NotAuthorizedResponse('Authentication failed');
        }

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

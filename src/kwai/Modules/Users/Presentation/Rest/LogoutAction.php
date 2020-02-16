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
use Kwai\Modules\Users\UseCases\Logout;
use Kwai\Modules\Users\UseCases\LogoutCommand;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Core\Responses\NotAuthorizedResponse;
use Core\Responses\OkResponse;

use Firebase\JWT\JWT;

/**
 * Revokes the current refreshtoken and the associated accesstoken.
 */
class LogoutAction
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

        $secret = $this->container->get('settings')['security']['secret'];
        $algorithm = $this->container->get('settings')['security']['algorithm'];

        $decodedRefreshToken = JWT::decode(
            $data['refresh_token'],
            $secret,
            [ $algorithm ]
        );

        $command = new LogoutCommand([
            'refresh_token_identifier' => $decodedRefreshToken->jti
        ]);

        try {
            (new Logout(
                new RefreshTokenDatabaseRepository($this->container->get('pdo_db')),
                new AccessTokenDatabaseRepository($this->container->get('pdo_db'))
            ))($command);
        } catch (NotFoundException $nfe) {
            return new NotAuthorizedResponse('Unknown refreshtoken');
        }

        return (new OkResponse())($response);
    }
}

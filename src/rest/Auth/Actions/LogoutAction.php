<?php

namespace REST\Auth\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\CryptTrait;

use Domain\Auth\RefreshTokenRepository;
use \Domain\Auth\AccessTokenRepository;

class LogoutAction
{
    use CryptTrait;

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $encKey = $this->container->get('settings')['encryption_key'];
        $this->setEncryptionKey($encKey);
        $server = $this->container->get('authorizationServer');

        $refreshTokenRepo = new RefreshTokenRepository();
        $token = json_decode($this->decrypt($request->getParsedBody()['refresh_token']));
        $refreshTokenRepo->revokeRefreshToken($token->refresh_token_id);

        $accessTokenRepo = new AccessTokenRepository();
        $accessTokenRepo->revokeAccessToken($token->access_token_id);

        return $response->withStatus(200);
    }
}

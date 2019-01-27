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
        $encKey = $this->container->get('settings')['oauth2']['encryption_key'];
        $this->setEncryptionKey($encKey);
        $server = $this->container->get('authorizationServer');

        $refreshTokenRepo = new RefreshTokenRepository();
        $refreshToken = $request->getParsedBody()['refresh_token'];
        if (isset($refreshToken)) {
            $token = json_decode($this->decrypt($refreshToken));
            $refreshTokenRepo->revokeRefreshToken($token->refresh_token_id);

            $accessTokenRepo = new AccessTokenRepository();
            $accessTokenRepo->revokeAccessToken($token->access_token_id);

            return $response->withStatus(200);
        }
        return $response->withStatus(400);
    }
}

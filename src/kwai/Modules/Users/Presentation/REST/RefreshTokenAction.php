<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\Responses\NotAuthorizedResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Firebase\JWT\JWT;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Kwai\Modules\Users\Domain\Exceptions\RefreshTokenNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\CreateRefreshToken;
use Kwai\Modules\Users\UseCases\CreateRefreshTokenCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class RefreshTokenAction
 *
 * Action to create a new refresh- and accesstoken (when the refreshtoken is valid).
 */
class RefreshTokenAction extends Action
{
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

        $secret = $this->getContainerEntry('settings')['security']['secret'];
        $algorithm = $this->getContainerEntry('settings')['security']['algorithm'];
        $decodedRefreshToken = JWT::decode(
            $data['refresh_token'],
            $secret,
            [ $algorithm ]
        );

        $command = new CreateRefreshTokenCommand();
        $command->identifier = $decodedRefreshToken->jti;

        try {
            $database = $this->getContainerEntry('pdo_db');
            $refreshToken = CreateRefreshToken::create(
                new RefreshTokenDatabaseRepository($database),
                new AccessTokenDatabaseRepository($database)
            )($command);
        } catch (AuthenticationException $ae) {
            return (new NotAuthorizedResponse('Authentication failed'))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (RefreshTokenNotFoundException $e) {
            return (new NotAuthorizedResponse('Unknown refreshtoken'))($response);
        }

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

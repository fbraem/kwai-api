<?php
/**
 * @package Kwai/Modules
 * @subpackage User
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\Logout;
use Kwai\Modules\Users\UseCases\LogoutCommand;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Responses\NotAuthorizedResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\OkResponse;

use Firebase\JWT\JWT;

/**
 * Class LogoutAction
 *
 * Action that revokes the current refreshtoken and the associated accesstoken.
 */
class LogoutAction extends Action
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

        $command = new LogoutCommand();
        $command->identifier = $decodedRefreshToken->jti;

        try {
            $database = $this->getContainerEntry('pdo_db');
            (new Logout(
                new RefreshTokenDatabaseRepository($database),
                new AccessTokenDatabaseRepository($database)
            ))($command);
        } catch (NotFoundException $nfe) {
            return (new NotAuthorizedResponse('Unknown refreshtoken'))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        return (new OkResponse())($response);
    }
}

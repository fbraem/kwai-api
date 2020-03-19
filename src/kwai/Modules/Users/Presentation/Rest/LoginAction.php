<?php
/**
 * @package Kwai/Modules
 * @subpackage User
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\NotAuthorizedResponse;
use Core\Responses\SimpleResponse;
use Firebase\JWT\JWT;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Login a user with email/pwd and return access- and refreshtoken on succes.
 */
class LoginAction extends Action
{
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
     * @param  string[] $args     Route’s named placeholders
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
            $database = $this->getContainerEntry('pdo_db');
            $refreshToken = (new AuthenticateUser(
                new UserDatabaseRepository($database),
                new AccessTokenDatabaseRepository($database),
                new RefreshTokenDatabaseRepository($database)
            ))($command);
        } catch (NotFoundException $nfe) {
            return (new NotAuthorizedResponse('Unknown user'))($response);
        } catch (AuthenticationException $ae) {
            return (new NotAuthorizedResponse('Authentication failed'))($response);
        }

        $secret = $this->getContainerEntry('settings')['security']['secret'];
        $algorithm = $this->getContainerEntry('settings')['security']['algorithm'];

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

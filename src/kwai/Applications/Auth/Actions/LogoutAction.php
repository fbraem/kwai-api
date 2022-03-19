<?php
/**
 * @package Applications
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Applications\Auth\Actions;

use Exception;
use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotAuthorizedResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\OkResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Security\JsonWebToken;
use Kwai\Modules\Users\Domain\Exceptions\RefreshTokenNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\Logout;
use Kwai\Modules\Users\UseCases\LogoutCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class LogoutAction
 *
 * Action that revokes the current refreshtoken and the associated accesstoken.
 */
#[Route(
    path: '/auth/logout',
    name: 'auth.logout',
    options: ['auth' => true],
    methods: ['POST']
)]
class LogoutAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        private ?Configuration $settings = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->settings ??= depends('kwai.settings', Settings::class);
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

        $secret = $this->settings->getSecurityConfiguration()->getSecret();
        $algorithm = $this->settings->getSecurityConfiguration()->getAlgorithm();

        if (!isset($data['refresh_token'])) {
            return (new SimpleResponse(400, 'Refreshtoken is missing'))($response);
        }

        try {
            $refreshToken = JsonWebToken::decode(
                $data['refresh_token'],
                $secret,
                $algorithm
            )->getObject();
        } catch (Exception $e) {
            return (new SimpleResponse(400, $e->getMessage()))($response);
        }

        $command = new LogoutCommand();
        $command->identifier = $refreshToken->jti;

        try {
            Logout::create(
                new RefreshTokenDatabaseRepository($this->database),
                new AccessTokenDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (RefreshTokenNotFoundException) {
            return (new NotAuthorizedResponse('Unknown refreshtoken'))($response);
        }

        return (new OkResponse())($response);
    }
}

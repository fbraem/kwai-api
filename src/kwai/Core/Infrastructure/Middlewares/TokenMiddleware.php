<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Middlewares;

use Exception;
use Firebase\JWT\ExpiredException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Configuration\SecurityConfiguration;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\LoggerDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Presentation\Responses\NotAuthorizedResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Security\JsonWebToken;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Monolog\Logger;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class TokenMiddleware
 *
 * Middleware for checking the access token.
 * This code is based on https://github.com/tuupola/slim-jwt-auth
 */
class TokenMiddleware implements MiddlewareInterface
{
    private SecurityConfiguration $config;

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private ?Configuration $settings = null,
        private ?Connection $db = null,
        private ?Logger $logger = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
        $this->config = $this->settings->getSecurityConfiguration();
        $this->db ??= depends('kwai.database', DatabaseDependency::class);
        $this->logger ??= depends('kwai.logger', LoggerDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Only check authentication when the route has an option with 'auth'
        // set to true.
        $extra = $request->getAttribute('kwai.extra') ?? ['auth' => false];
        $shouldAuthenticate = $extra['auth'] ?? false;
        if (!$shouldAuthenticate) {
            return $handler->handle($request);
        }

        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();

        /* HTTP allowed only if secure is false or server is in relaxed array. */
        if ("https" !== $scheme) {
            if (!in_array($host, $this->config->getRelaxed())) {
                $message = sprintf(
                    "Insecure use of middleware over %s denied by configuration.",
                    strtoupper($scheme)
                );
                $response = $this->responseFactory->createResponse();
                return (new NotAuthorizedResponse($message))($response);
            }
        }

        // Try to find the token in header or cookie
        $token = $this->fetchToken($request);
        if ($token === null) {
            $response = $this->responseFactory->createResponse();
            return (new SimpleResponse(400, 'Token not found'))($response);
        }

        // Try to decode the token
        try {
            $decoded = JsonWebToken::decode(
                $token,
                $this->config->getSecret(),
                $this->config->getAlgorithm()
            )->getObject();
        } catch (ExpiredException) {
            $response = $this->responseFactory->createResponse();
            return (new NotAuthorizedResponse('Token is expired'))($response);
        } catch (Exception $exception) {
            $this->logger->warning($exception->getMessage(), [$token]);
            $response = $this->responseFactory->createResponse();
            return (new SimpleResponse(400, 'Token could not be decoded'))($response);
        }

        $uuid = new UniqueId($decoded->sub);
        $userRepo = new UserDatabaseRepository($this->db);
        try {
            $user = $userRepo->getByUniqueId($uuid);
            $request = $request->withAttribute('kwai.user', $user);
        } catch (UserNotFoundException) {
            $this->logger->warning("User with uuid $uuid does not exist");
            $response = $this->responseFactory->createResponse();
            return (new SimpleResponse(400, 'User not found'))($response);
        }

        // Everything is ok, let the next middleware do its thing
        return $handler->handle($request);
    }

    /**
     * Fetch the access token.
     */
    private function fetchToken(ServerRequestInterface $request): ?string
    {
        $regexp = $this->config->getRegex();

        /* Check for token in header. */
        $headerName = $this->config->getHeader();
        $header = $request->getHeaderLine($headerName);

        if (false === empty($header)) {
            if (preg_match($regexp, $header, $matches)) {
                $this->logger->debug('Using token from request header', [$headerName]);
                return $matches[1];
            }
        }

        /* Token not found in header try a cookie. */
        $cookieParams = $request->getCookieParams();
        $cookieName = $this->config->getCookie();
        if (isset($cookieParams[$cookieName])) {
            $this->logger->debug('Using token from cookie', [$cookieName]);
            if (preg_match(
                $regexp,
                $cookieParams[$cookieName],
                $matches
            )) {
                return $matches[1];
            }
            return $cookieParams[$cookieName];
        };

        return null;
    }
}

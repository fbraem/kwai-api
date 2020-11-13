<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class PreflightAction
 *
 * Action to handle a preflight request.
 */
final class PreflightAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return $response;
    }
}

<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class OkResponse
 *
 * Returns a response with code 200.
 */
class OkResponse
{
    public function __invoke(Response $response) : Response
    {
        return $response->withStatus(200);
    }
}

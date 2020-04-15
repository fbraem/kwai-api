<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class JSONResponse
 *
 * Returns a response with JSON data and content-type set to 'application/json'.
 */
class JSONResponse
{
    /**
     * The JSON data.
     * @var mixed
     */
    private $data;

    /**
     * JSONResponse constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Writes the JSON data to the response and sets the content-type header
     * to 'application/json'.
     *
     * @param Response $response
     * @return Response
     */
    public function __invoke(Response $response) : Response
    {
        $response->getBody()->write(json_encode($this->data));
        return $response->withHeader('content-type', 'application/json');
    }
}

<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Kwai\JSONAPI;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class JSONAPIResponse
 *
 * Returns a JSON:API response.
 */
class JSONAPIResponse
{
    public function __construct(
        private JSONAPI\Document $document
    ) {
    }

    /**
     * Writes the JSONAPI document to the response and sets the content-type
     * header to 'application/json'.
     *
     * @param Response $response
     * @return Response
     */
    public function __invoke(Response $response) : Response
    {
        try {
            $json = $this->document->serialize();
        } catch (JSONAPI\Exception $e) {
            return $response
                ->withStatus(500, 'A JSONAPI exception occurred');
        }
        $response->getBody()->write($json);
        return $response->withHeader('content-type', 'application/json');
    }
}

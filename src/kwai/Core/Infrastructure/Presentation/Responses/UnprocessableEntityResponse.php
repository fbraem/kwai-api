<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class UnprocessableEntityResponse
 *
 * Creates a response with JSONAPI error structure.
 * The status will be set to 422 and the content-type header to 'application/vnd.api+json'.
 */
class UnprocessableEntityResponse
{
    private array $errors;

    /**
     * UnprocessableEntityResponse constructor.
     *
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function __invoke(Response $response) : Response
    {
        $errors = [];
        foreach ($this->errors as $pointer => $messages) {
            if (is_array($messages)) {
                foreach ($messages as $message) {
                    $errors[] = $this->createJSONAPIError($pointer, $message);
                }
            } else {
                $errors[] = $this->createJSONAPIError($pointer, $messages);
            }
        }
        $response
            ->getBody()
            ->write(json_encode(['errors' => $errors]))
        ;

        return $response
            ->withStatus(422)
            ->withHeader('content-type', 'application/vnd.api+json')
        ;
    }

    private function createJSONAPIError($pointer, $message)
    {
        return [
            'source' => [
                'pointer' => $pointer
            ],
            'title' => $message
        ];
    }
}

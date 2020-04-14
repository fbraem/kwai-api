<?php

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

class UnprocessableEntityResponse
{
    private $errors;

    public function __construct($errors)
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

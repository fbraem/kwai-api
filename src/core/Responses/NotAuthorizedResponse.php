<?php

namespace Core\Responses;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * NotAuthorizedResponse is a response with status 401 Unauthorized.
 */
class NotAuthorizedResponse
{
    /**
     * Status message
     * @var string
     */
    private $message;

    /**
     * Constructor
     * @param string $message
     */
    public function __construct($message = 'Not Authorized')
    {
        $this->message = $message;
    }

    /**
     * Returns a response with status code 401 and a status message.
     * @param  Response $response
     * @return Response
     */
    public function __invoke(Response $response) : Response
    {
        return $response
            ->withStatus(401, $this->message);
        ;
    }
}

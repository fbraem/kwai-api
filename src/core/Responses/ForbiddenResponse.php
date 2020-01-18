<?php

namespace Core\Responses;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * ForbiddenResponse is returned when an action is not allowed.
 * It means: we know who you are, but you are not allowed to.
 */
class ForbiddenResponse
{
    /**
     * A status message
     * @var string
     */
    private $message;

    /**
     * Constructor
     * @param string $message
     */
    public function __construct($message = 'Action is forbidden')
    {
        $this->message = $message;
    }

    /**
     * Returns a response with status 403 and a status message.
     * @param  Response $response
     * @return Response
     */
    public function __invoke(Response $response) : Response
    {
        return $response
            ->withStatus(403, $this->message);
        ;
    }
}

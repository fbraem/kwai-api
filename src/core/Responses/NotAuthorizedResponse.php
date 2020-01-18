<?php

namespace Core\Responses;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * NotAuthorizedResponse is a response with status 401 Unauthorized.
 */
class NotAuthorizedResponse extends SimpleResponse
{
    /**
     * Constructor
     * @param string $message
     */
    public function __construct($message = 'Not Authorized')
    {
        parent::__construct(401, $message);
    }
}

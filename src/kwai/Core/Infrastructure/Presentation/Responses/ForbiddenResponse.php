<?php

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * ForbiddenResponse is returned when an action is not allowed.
 * It means: we know who you are, but you are not allowed to.
 */
class ForbiddenResponse extends SimpleResponse
{
    /**
     * Constructor
     * @param string $message
     */
    public function __construct($message = 'Action is forbidden')
    {
        parent::construct(403, $message);
    }
}

<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

/**
 * Class NotAuthorizedResponse
 *
 * NotAuthorizedResponse is a response with status 401 Unauthorized.
 */
class NotAuthorizedResponse extends SimpleResponse
{
    /**
     * Constructor
     *
     * @param string $message
     */
    public function __construct($message = 'Not Authorized')
    {
        parent::__construct(401, $message);
    }
}

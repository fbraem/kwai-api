<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class SimpleResponse
 *
 * When invoked, it will return a response with a status and message.
 */
class SimpleResponse
{
    private int $status;
    private string $message;

    /**
     * SimpleResponse constructor.
     *
     * @param int    $status
     * @param string $message
     */
    public function __construct(int $status, string $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Add a status and message to the response and return it.
     *
     * @param Response $response
     * @return Response
     */
    public function __invoke(Response $response) : Response
    {
        return $response
            ->withStatus($this->status, $this->message)
        ;
    }
}

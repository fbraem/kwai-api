<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

/**
 * Class NotFoundResponse
 *
 * Returns a response with status 404.
 */
class NotFoundResponse extends SimpleResponse
{
    public function __construct(string $message)
    {
        parent::__construct(404, $message);
    }
}

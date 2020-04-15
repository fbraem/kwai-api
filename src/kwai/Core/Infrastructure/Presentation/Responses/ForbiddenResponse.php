<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

/**
 * Class ForbiddenResponse
 *
 * ForbiddenResponse is returned when an action is not allowed.
 * It means: we know who you are, but you are not allowed to.
 */
class ForbiddenResponse extends SimpleResponse
{
    /**
     * ForbiddenResponse constructor
     *
     * @param string $message
     */
    public function __construct(string $message = 'Action is forbidden')
    {
        parent::__construct(403, $message);
    }
}

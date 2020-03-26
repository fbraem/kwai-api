<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class GetUserCommand
 *
 * Command for the GetUser use case.
 */
class GetUserCommand
{
    /**
     * The unique id of a user.
     */
    public string $uuid;
}

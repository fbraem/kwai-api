<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

/**
 * GetCurrentUserCommand is a datatransferobject for the GetCurrentUser usecase.
 */
final class GetCurrentUserCommand
{
    /**
     * Unique ID of the user
     */
    public string $uuid;
}

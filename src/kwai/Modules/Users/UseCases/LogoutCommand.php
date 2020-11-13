<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

/**
 * LogoutCommand is a datatransferobject for the Logout usecase.
 */
final class LogoutCommand
{
    /**
     * The identifier of the refreshtoken
     */
    public string $identifier;
}

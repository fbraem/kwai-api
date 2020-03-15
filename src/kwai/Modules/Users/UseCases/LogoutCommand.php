<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

/**
 * LogoutCommand is a DataTransferObject for the Logout usecase.
 */
final class LogoutCommand
{
    /**
     * The identifier of the refreshtoken
     * @var string
     */
    public string $identifier;
}

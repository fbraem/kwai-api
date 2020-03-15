<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

/**
 * CreateRefreshTokenCommand is a DataTransferObject for the CreateRefreshToken usecase.
 */
final class CreateRefreshTokenCommand
{
    /**
     * The identifier of the refreshtoken
     */
    public string $identifier;
}

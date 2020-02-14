<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * CreateRefreshTokenCommand is a DataTransferObject for the
 * CreateRefreshToken usecase.
 */
final class CreateRefreshTokenCommand extends DataTransferObject
{
    /**
     * The identifier of the refreshtoken
     * @var string
     */
    public $refresh_token_identifier;
}

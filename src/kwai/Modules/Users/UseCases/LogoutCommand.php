<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * LogoutCommand is a DataTransferObject for the
 * Logout usecase.
 */
final class LogoutCommand extends DataTransferObject
{
    /**
     * The identifier of the refreshtoken
     * @var string
     */
    public $refresh_token_identifier;
}

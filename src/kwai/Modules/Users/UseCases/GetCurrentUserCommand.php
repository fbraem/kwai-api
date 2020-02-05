<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * GetCurrentUserCommand is a DataTransferObject for the GetCurrentUser usecase.
 */
final class GetCurrentUserCommand extends DataTransferObject
{
    /**
     * Unique ID of the user
     * @var string
     */
    public $uuid;
}

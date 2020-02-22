<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

/**
 * InviteUserCommand is a DataTransferObject for the InviteUser usecase.
 */
final class InviteUserCommand extends FlexibleDataTransferObject
{
    /**
     * Email
     * @var string
     */
    public $email;

    /**
     * Number of days before the invitation expires
     * @var int
     */
    public $expiration;

    /**
     * Remark
     * @var string|null
     */
    public $remark;

    /**
     * Username
     * @var string
     */
    public $name;
}

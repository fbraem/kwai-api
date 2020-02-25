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
     */
    public string $email;

    /**
     * Number of days before the invitation expires
     */
    public int $expiration;

    /**
     * Remark
     */
    public ?string $remark;

    /**
     * Username
     */
    public string $name;
}

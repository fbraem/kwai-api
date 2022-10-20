<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * ConfirmInvitationCommand
 */
final class ConfirmInvitationCommand
{
    /**
     * The unique id of the invitation
     */
    public string $uuid;

    /**
     * A remark
     */
    public ?string $remark;

    /**
     * The first name
     */
    public string $firstName;

    /**
     * The last name
     */
    public string $lastName;

    /**
     * The password
     */
    public string $password;
}

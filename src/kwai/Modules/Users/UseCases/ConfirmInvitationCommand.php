<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * ConfirmInvitationCommand is a datatransferobject for the usecase ConfirmInvitation
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

    /**
     * The email of the new user
     */
    public string $email;
}

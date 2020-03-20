<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

/**
 * InviteUserCommand is a datatransferobject for the usecase InviteUser.
 */
final class InviteUserCommand
{
    /**
     * Email of the sender
     */
    public string $sender_mail;

    /**
     * The name of the sender;
     */
    public string $sender_name;

    /**
     * Email of the recipient
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

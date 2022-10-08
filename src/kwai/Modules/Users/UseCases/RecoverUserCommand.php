<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

final class RecoverUserCommand
{
    public string $email;

    public int $expiration = 2;
}
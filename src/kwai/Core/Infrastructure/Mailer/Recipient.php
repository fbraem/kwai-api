<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

/**
 * Class Recipient
 */
class Recipient implements Identity
{
    public function __construct(
        private readonly string $email,
        private readonly string $name = ''
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
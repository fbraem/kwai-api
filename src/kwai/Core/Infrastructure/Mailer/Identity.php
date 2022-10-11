<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

/**
 * Interface Identity
 */
interface Identity
{
    public function getEmail(): string;

    public function getName(): string;
}
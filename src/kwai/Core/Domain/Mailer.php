<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain;

use Kwai\Core\Infrastructure\Mailer\Message;

/**
 * Interface Mailer
 *
 * Interface for a class that sends an email in the domain.
 */
interface Mailer
{
    public function send(): Message;
}
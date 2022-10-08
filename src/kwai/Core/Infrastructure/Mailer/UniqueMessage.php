<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Symfony\Component\Mime\Email;

/**
 * Class UniqueMessage
 */
class UniqueMessage implements Message
{
    public function __construct(
        private readonly TemplatedMessage $message,
        private readonly UniqueId $uniqueId = new UniqueId()
    ) {
    }

    public function getUniqueId(): UniqueId
    {
        return $this->uniqueId;
    }

    /**
     * @inheritDoc
     */
    public function processMail(Email $email): Email
    {
        $this->message->addVariable('uuid', (string) $this->uniqueId);
        return $this->message->processMail($email);
    }
}
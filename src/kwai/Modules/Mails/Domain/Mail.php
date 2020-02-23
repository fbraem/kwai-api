<?php
/**
 * @package Kwai/Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain;

use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Kwai\Modules\Users\Domain\User;

/**
 * Mail Entity
 */
class Mail implements DomainEntity
{
    /**
     * Tag of the email
     * @var string
     */
    private $tag;

    /**
     * Uniqued ID for this mail
     * @var UniqueId
     */
    private $uuid;

    /**
     * Sender
     * @var Address
     */
    private $sender;

    /**
     * Content of the mail
     * @var MailContent
     */
    private $content;

    /**
     * Time of sending
     * @var Timestamp
     */
    private $sentTime;

    /**
     * Remark
     * @var string
     */
    private $remark;

    /**
     * User that created this mail
     * @var Entity<User>
     */
    private $creator;

    /**
     * Track create & modify times
     * @var TraceableTime
     */
    private $traceableTime;

    /**
     * The recipients
     * @var Recipient[]
     */
    private $recipients;

    /**
     * Constructor
     * @param object $props Mail properties
     */
    public function __construct(object $props)
    {
        $this->tag = $props->tag;
        $this->uuid = $props->uuid;
        $this->sender = $props->sender;
        $this->content = $props->content;
        $this->sentTime = $props->sentTime;
        $this->remark = $props->remark;
        $this->creator = $props->creator;
        $this->traceableTime = $props->traceableTime;
        $this->recipients = $props->recipients;
    }

    /**
     * Add recipient
     * @param Recipient $recipient
     */
    public function addRecipient(Recipient $recipient): void
    {
        $this->recipients[] = $recipient;
    }
}

<?php
/**
 * @package Pages
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\TraceableTime;

/**
 * The Page entity
 */
class Page implements DomainEntity
{
    /**
     * Is this page enabled?
     */
    private bool $enabled;

    /**
     * The content.
     * @var Text[]
     */
    private array $contents;

    /**
     * Associated images
     * @var string[]
     */
    private array $images = [];

    /**
     * Priority of the page. Used for sorting.
     */
    private int $priority;

    /**
     * A remark
     */
    private ?string $remark;

    /**
     * When is this page created/updated?
     */
    private TraceableTime $traceableTime;

    /**
     * Page constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->enabled = $props->enabled ?? false;
        $this->contents = $props->contents ?? [];
        $this->images = $props->images ?? [];
        $this->priority = $props->priority ?? 0;
        $this->remark = $props->remark ?? null;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
    }

    /**
     * Attach images
     *
     * @param string[] $images
     */
    public function attachImages(array $images)
    {
        $this->images = $images;
    }

    /**
     * Add content
     *
     * @param Text $content
     */
    public function addContent(Text $content)
    {
        $this->contents[] = $content;
    }

    /**
     * Is this page enabled?
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get the contents
     *
     * @return Text[]
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * Get all attached images
     *
     * @return string[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Get the priority
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Get the remark
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Get the create/update timestamps
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }
}

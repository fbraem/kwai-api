<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;

/**
 * Class Story
 */
class Story implements DomainEntity
{
    /**
     * Is this story enabled?
     */
    private bool $enabled;

    /**
     * The promotion
     */
    private Promotion $promotion;

    /**
     * When will the story be published?
     */
    private Timestamp $publishTime;

    /**
     * When will the story be unpublished?
     */
    private ?Timestamp $endDate;

    private ?string $remark;

    private TraceableTime $traceableTime;

    /**
     * @var Entity<Category>
     */
    private Entity $category;

    /**
     * @var Text[]
     */
    private array $contents;

    /**
     * @var string[]
     */
    private array $images;

    public function __construct(object $props)
    {
        $this->enabled = $props->enabled;
        $this->promotion = $props->promotion ?? new Promotion();
        $this->publishTime = $props->publishTime;
        $this->endDate = $props->endDate ?? null;
        $this->remark = $props->remark ?? null;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
        $this->category = $props->category;
        $this->contents = $props->contents ?? [];
    }

    public function attachImages(array $images)
    {
        $this->images = $images;
    }
}

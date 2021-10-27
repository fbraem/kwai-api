<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
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
     * Story constructor.
     *
     * @param LocalTimestamp      $publishTime
     * @param Entity              $application
     * @param Collection          $contents
     * @param Promotion|null      $promotion
     * @param Collection|null     $images
     * @param bool                $enabled
     * @param LocalTimestamp|null $endDate
     * @param string|null         $remark
     * @param TraceableTime|null  $traceableTime
     */
    public function __construct(
        private LocalTimestamp $publishTime,
        private Entity $application,
        private Collection $contents,
        private ?Promotion $promotion = null,
        private ?Collection $images = null,
        private bool $enabled = false,
        private ?LocalTimestamp $endDate = null,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null,
    ) {
        $this->promotion ??= new Promotion();
        $this->traceableTime ??= new TraceableTime();
        $this->images ??= new Collection();
    }

    /**
     * Attach images
     *
     * @param Collection $images
     */
    public function attachImages(Collection $images)
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
        $this->contents->add($content);
    }

    /**
     * Is this story enabled?
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Return the promotion
     */
    public function getPromotion(): Promotion
    {
        return $this->promotion;
    }

    /**
     * Return the end date
     */
    public function getEndDate(): ?LocalTimestamp
    {
        return $this->endDate;
    }

    /**
     * Return the remark
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Return the traceable time
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Return the category
     *
     * @return Entity<Application>
     */
    public function getApplication(): Entity
    {
        return $this->application;
    }

    /**
     * Return the content
     *
     * @return Collection
     */
    public function getContents(): Collection
    {
        return $this->contents->collect();
    }

    /**
     * Return the publish time
     */
    public function getPublishTime(): LocalTimestamp
    {
        return $this->publishTime;
    }

    /**
     * Return the associated images
     *
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->images->collect();
    }
}

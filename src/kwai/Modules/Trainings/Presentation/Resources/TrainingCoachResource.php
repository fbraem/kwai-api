<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;

/**
 * Class TrainingCoachResource
 */
#[JSONAPI\Resource(type: 'training_coaches', id: 'getId')]
class TrainingCoachResource
{
    public function __construct(
        private TrainingCoach $coach
    ) {
    }

    public function getId(): string
    {
        return (string) $this->coach->getCoach()->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return (string) $this->coach->getCoach()->getName();
    }

    #[JSONAPI\Attribute(name: 'present')]
    public function isPresent(): bool
    {
        return $this->coach->isPresent();
    }

    #[JSONAPI\Attribute(name: 'head')]
    public function isHead(): bool
    {
        return $this->coach->isHead();
    }

    #[JSONAPI\Attribute(name: 'payed')]
    public function isPayed(): bool
    {
        return $this->coach->isPayed();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->coach->getRemark();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->coach->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->coach->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}

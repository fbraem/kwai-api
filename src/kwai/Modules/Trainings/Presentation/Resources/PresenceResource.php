<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\ValueObjects\Presence;

/**
 * Class PresenceResource
 */
#[JSONAPI\Resource(type: 'presences', id: 'getId')]
class PresenceResource
{

    public function __construct(
        private Presence $presence
    ) {
    }

    public function getId()
    {
        return (string) $this->presence->getMember()->id();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->presence->getRemark();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return (string) $this->presence->getMember()->getName();
    }

    #[JSONAPI\Attribute(name: 'gender')]
    public function getGender(): int
    {
        return $this->presence->getMember()->getGender()->getValue();
    }

    #[JSONAPI\Attribute(name: 'birthdate')]
    public function getBirthDate(): string
    {
        return (string) $this->presence->getMember()->getBirthDate();
    }

    #[JSONAPI\Attribute(name: 'license')]
    public function getLicense(): string
    {
        return (string) $this->presence->getMember()->getLicense();
    }

    #[JSONAPI\Attribute(name: 'license_end_date')]
    public function getLicenseEndDate(): string
    {
        return (string) $this->presence->getMember()->getLicenseEndDate();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->presence->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->presence->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}

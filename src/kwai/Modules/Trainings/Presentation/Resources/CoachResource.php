<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Coach;

/**
 * Class CoachResource
 *
 * A JSON:API resource for a Coach entity.
 */
#[JSONAPI\Resource(type: 'coaches', id: 'getId')]
class CoachResource
{
    /**
     * @param Entity<Coach> $coach
     */
    public function __construct(
        private Entity $coach
    ) {
    }

    public function getId(): string
    {
        return (string) $this->coach->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return (string) $this->coach->getName();
    }
}

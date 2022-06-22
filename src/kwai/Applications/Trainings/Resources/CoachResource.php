<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\CoachEntity;

/**
 * Class CoachResource
 *
 * A JSON:API resource for a Coach entity.
 */
#[JSONAPI\Resource(type: 'coaches', id: 'getId')]
class CoachResource
{
    public function __construct(
        private CoachEntity $coach
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

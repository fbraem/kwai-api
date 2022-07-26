<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\SeasonEntity;

/**
 * Class SeasonResource
 */
#[JSONAPI\Resource(type: 'seasons', id: 'getId')]
class SeasonResource
{
    public function __construct(
        private SeasonEntity $season
    ) {
    }

    public function getId(): string
    {
        return (string) $this->season->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->season->getName();
    }
}

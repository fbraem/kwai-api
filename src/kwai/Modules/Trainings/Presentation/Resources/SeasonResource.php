<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Season;

/**
 * Class SeasonResource
 */
#[JSONAPI\Resource(type: 'seasons', id: 'getId')]
class SeasonResource
{
    /**
     * @param Entity<Season> $season
     */
    public function __construct(
        private Entity $season
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

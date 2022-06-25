<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Modules\Trainings\Domain\Season;
use Kwai\Modules\Trainings\Domain\SeasonEntity;
use Kwai\Modules\Trainings\Infrastructure\SeasonsTable;

/**
 * Class SeasonDTO
 */
class SeasonDTO
{
    public function __construct(
        public SeasonsTable $season = new SeasonsTable()
    ) {
    }

    public function create(): Season
    {
        return new Season(
            $this->season->name
        );
    }

    public function createEntity(): SeasonEntity
    {
        return new SeasonEntity(
            $this->season->id,
            $this->create()
        );
    }
}

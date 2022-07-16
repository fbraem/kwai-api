<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Modules\Trainings\Domain\ValueObjects\Presence;
use Kwai\Modules\Trainings\Infrastructure\TrainingPresencesTable;

class PresenceDTO
{
    public function __construct(
        public TrainingPresencesTable $presenceTable = new TrainingPresencesTable(),
        public MemberDTO $memberDTO = new MemberDTO(),
        public CreatorDTO $creatorDTO = new CreatorDTO()
    ) {
    }

    public function create(): Presence
    {
        return new Presence(
            member: $this->memberDTO->createEntity(),
            creator: $this->creatorDTO->create()
        );
    }
}

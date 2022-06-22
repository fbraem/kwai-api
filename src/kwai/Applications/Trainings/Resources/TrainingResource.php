<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;

/**
 * Class TrainingResource
 */
#[JSONAPI\Resource(type: 'trainings', id: 'getId')]
class TrainingResource
{
    /**
     * @param Entity<Training> $training
     * @param ConverterFactory $converterFactory
     */
    public function __construct(
        private Entity           $training,
        private ConverterFactory $converterFactory
    ) {
    }

    public function getId(): string
    {
        return (string)$this->training->id();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->training->getRemark();
    }

    #[JSONAPI\Attribute(name: 'event')]
    public function getEvent(): array
    {
        $event = $this->training->getEvent();
        return [
            'start_date' => (string)$event->getStartDate(),
            'end_date' => (string)$event->getEndDate(),
            'timezone' => $event->getTimezone(),
            'location' => $event->getLocation() ? (string)$event->getLocation() : null,
            'cancelled' => $event->isCancelled(),
            'active' => $event->isActive()
        ];
    }

    #[JSONAPI\Attribute(name: 'contents')]
    public function getContents(): array
    {
        return $this->training->getText()->map(function (Text $text) {
            $converter = $this->converterFactory
                ->createConverter($text->getFormat());
            return [
                'locale' => $text->getLocale()->value,
                'title' => $text->getTitle(),
                'summary' => $text->getSummary(),
                'content' => $text->getContent(),
                'html_summary' => $converter->convert($text->getSummary()),
                'html_content' => $converter->convert($text->getContent() ?? ''),
            ];
        })->toArray();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string)$this->training->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->training->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Attribute(name: 'coaches')]
    public function getTrainingCoaches(): array
    {
        // Coaches are stored with the id as key, to return an array
        // values is used to turn it into a normal array.
        return $this->training->getCoaches()->map(
            fn(TrainingCoach $coach) => (object) [
                'id' => (string) $coach->getCoach()->id(),
                'head' => $coach->isHead(),
                'present' => $coach->isPresent(),
                'payed' => $coach->isPayed(),
                'remark' => $coach->getRemark()
            ]
        )->values()->all();
    }

    #[JSONAPI\Relationship(name: 'coaches')]
    public function getCoaches(): array
    {
        return $this->training->getCoaches()->map(
            fn(TrainingCoach $coach) => new CoachResource($coach->getCoach())
        )->toArray();
    }

    #[JSONAPI\Relationship(name: 'teams')]
    public function getTeams(): array
    {
        return $this->training->getTeams()->map(
            fn(Entity $team) => new TeamResource($team)
        )->toArray();
    }

    #[JSONAPI\Relationship(name: 'definition')]
    public function getDefinition(): ?DefinitionResource
    {
        $definition = $this->training->getDefinition();
        if ($definition) {
            return new DefinitionResource($definition);
        }
        return null;
    }
}

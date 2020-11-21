<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\ValueObjects;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Modules\Trainings\Domain\Coach;

/**
 * Class TrainingCoach
 *
 * A value object for a coach that is appointed to a training.
 */
class TrainingCoach
{
    /**
     * @var Entity<Coach>
     */
    private Entity $coach;

    private bool $head;

    private bool $present;

    private bool $payed;

    private ?string $remark;

    private Creator $creator;

    /**
     * TrainingCoach constructor.
     *
     * @param Entity  $coach
     * @param bool    $head
     * @param bool    $present
     * @param bool    $payed
     * @param Creator $creator
     * @param ?string $remark
     */
    public function __construct(
        Entity $coach,
        bool $head,
        bool $present,
        bool $payed,
        Creator $creator,
        string $remark = null
    ) {
        $this->coach = $coach;
        $this->head = $head;
        $this->present = $present;
        $this->payed = $payed;
        $this->remark = $remark;
        $this->creator = $creator;
    }

    /**
     * @return Entity
     */
    public function getCoach(): Entity
    {
        return $this->coach;
    }

    /**
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->head;
    }

    /**
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->present;
    }

    /**
     * @return bool
     */
    public function isPayed(): bool
    {
        return $this->payed;
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @return Creator
     */
    public function getCreator(): Creator
    {
        return $this->creator;
    }
}

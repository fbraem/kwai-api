<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\ValueObjects;

use Kwai\Core\Domain\Entity;
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

    /**
     * TrainingCoach constructor.
     *
     * @param Entity<Coach>  $coach
     * @param bool    $head
     * @param bool    $present
     * @param bool    $payed
     * @param ?string $remark
     */
    public function __construct(
        Entity $coach,
        bool $head,
        bool $present,
        bool $payed,
        string $remark = null
    ) {
        $this->coach = $coach;
        $this->head = $head;
        $this->present = $present;
        $this->payed = $payed;
        $this->remark = $remark;
    }

    /**
     * @return Entity<Coach>
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
}

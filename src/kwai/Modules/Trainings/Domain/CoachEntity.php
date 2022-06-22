<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class TrainingEntity
 * @mixin Coach
 */
class CoachEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly Coach $domain
    ) {
    }

    public function domain(): Coach
    {
        return $this->domain;
    }
}

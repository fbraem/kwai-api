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
 * @mixin Training
 */
class TrainingEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly Training $domain
    ) {
    }

    public function domain(): Training
    {
        return $this->domain;
    }
}

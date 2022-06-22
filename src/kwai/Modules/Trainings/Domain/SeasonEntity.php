<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class SeasonEntity
 * @mixin Season
 */
class SeasonEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly Season $domain
    ) {
    }

    public function domain(): Season
    {
        return $this->domain;
    }
}

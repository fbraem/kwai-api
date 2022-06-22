<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class TeamEntity
 * @mixin Team
 */
class TeamEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly Team $domain
    ) {
    }

    public function domain(): Team
    {
        return $this->domain;
    }
}

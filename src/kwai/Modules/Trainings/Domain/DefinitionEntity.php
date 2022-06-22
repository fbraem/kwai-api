<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class DefinitionEntity
 * @mixin Definition
 */
class DefinitionEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly Definition $domain
    ) {
    }

    public function domain(): Definition
    {
        return $this->domain;
    }
}

<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class MemberEntity
 * @mixin Member
 */
class MemberEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly Member $domain
    ) {
    }

    public function domain(): Member
    {
        return $this->domain;
    }
}

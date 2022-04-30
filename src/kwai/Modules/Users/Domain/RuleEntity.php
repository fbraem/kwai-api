<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class RuleEntity
 * @mixin Rule
 */
final class RuleEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly Rule $domain
    ) {
    }

    public function domain(): Rule
    {
        return $this->domain;
    }
}

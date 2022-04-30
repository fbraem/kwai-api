<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class UserAccountEntity
 *
 * @mixin UserAccount
 */
final class UserAccountEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly UserAccount $domain
    ) {
    }

    public function domain()
    {
        return $this->domain();
    }
}

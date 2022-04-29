<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class RefreshTokenEntity
 *
 * @mixin RefreshToken
 */
class RefreshTokenEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly RefreshToken $domain
    ) {
    }

    public function domain(): RefreshToken
    {
        return $this->domain;
    }
}

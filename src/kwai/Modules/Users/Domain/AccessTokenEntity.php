<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class AccessTokenEntity
 */
class AccessTokenEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly AccessToken $domain
    ) {
    }

    public function domain(): AccessToken
    {
        return $this->domain;
    }
}

<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
 * Interface RefreshTokenQuery
 */
interface RefreshTokenQuery extends Query
{
    /**
     * Filter on token identifier
     *
     * @param TokenIdentifier $identifier
     * @return RefreshTokenQuery
     */
    public function filterTokenIdentifier(TokenIdentifier $identifier): self;
}

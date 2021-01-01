<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Repositories;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface MailQuery
 */
interface MailQuery extends Query
{
    /**
     * Filter on id
     *
     * @param int $id
     * @return $this
     */
    public function filterId(int $id): self;

    /**
     * Filter on unique id
     *
     * @param UniqueId $uniqueId
     * @return $this
     */
    public function filterUUID(UniqueId $uniqueId): self;
}

<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface RecipientQuery
 */
interface RecipientQuery extends Query
{
    /**
     * Filter all recipients for the given mails
     *
     * @param int ...$id
     * @return $this
     */
    public function filterMail(int ...$id): self;
}

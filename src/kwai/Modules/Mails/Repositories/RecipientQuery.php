<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface RecipientQuery
 */
interface RecipientQuery extends Query
{
    /**
     * Filter all recipients for the given mails
     *
     * @param Collection $ids
     * @return $this
     */
    public function filterOnMails(Collection $ids): self;
}

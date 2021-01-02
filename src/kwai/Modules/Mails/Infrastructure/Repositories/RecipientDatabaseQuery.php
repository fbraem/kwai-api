<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Mails\Infrastructure\Tables;
use Kwai\Modules\Mails\Repositories\RecipientQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class RecipientDatabaseQuery
 *
 * Class for querying mail recipients.
 */
class RecipientDatabaseQuery extends DatabaseQuery implements RecipientQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from((string) Tables::RECIPIENTS())
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $recipientsAliasFn = Tables::RECIPIENTS()->getAliasFn();

        return [
            $recipientsAliasFn('id'),
            $recipientsAliasFn('mail_id'),
            $recipientsAliasFn('type'),
            $recipientsAliasFn('email'),
            $recipientsAliasFn('name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterOnMails(Collection $ids): RecipientQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::RECIPIENTS()->mail_id)->in(...$ids->toArray())
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $prefixes = collect([
            Tables::RECIPIENTS()->getAliasPrefix()
        ]);

        $mails = new Collection();

        foreach ($rows as $row) {
            [ $recipient ] = $row->filterColumns($prefixes);
            if (! $mails->has($recipient['mail_id'])) {
                $mails->put($recipient['mail_id'], new Collection());
            }
            $mails[$recipient['mail_id']]->put(
                $recipient['id'], $recipient
            );
        }

        return $mails;
    }
}

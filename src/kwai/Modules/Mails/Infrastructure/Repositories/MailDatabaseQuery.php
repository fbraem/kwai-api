<?php
/**
 * @package Modules
 * @subpackage Mail
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Mails\Infrastructure\Tables;
use Kwai\Modules\Mails\Repositories\MailQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class MailDatabaseQuery
 */
class MailDatabaseQuery extends DatabaseQuery implements MailQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::MAILS())
            ->join(
                (string) Tables::USERS(),
                on(
                    Tables::MAILS()->user_id,
                    Tables::USERS()->id
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasMailFn = Tables::MAILS()->getAliasFn();
        $aliasUserFn = Tables::USERS()->getAliasFn();

        return [
            $aliasMailFn('id'),
            $aliasMailFn('tag'),
            $aliasMailFn('uuid'),
            $aliasMailFn('sender_email'),
            $aliasMailFn('sender_name'),
            $aliasMailFn('subject'),
            $aliasMailFn('html_body'),
            $aliasMailFn('text_body'),
            $aliasMailFn('sent_time'),
            $aliasMailFn('remark'),
            $aliasMailFn('user_id'),
            $aliasMailFn('created_at'),
            $aliasMailFn('updated_at'),
            $aliasUserFn('id'),
            $aliasUserFn('first_name'),
            $aliasUserFn('last_name'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): MailQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->where(
                field(Tables::MAILS()->id)->eq($id)
            )
        ;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterUUID(UniqueId $uniqueId): MailQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->where(
                field(Tables::MAILS()->uuid)->eq((string) $uniqueId)
            )
        ;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $filters = collect([
            Tables::MAILS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ]);

        $mails = new Collection();

        $rows = parent::walk($limit, $offset);
        foreach ($rows as $row) {
            [ $mail, $user ] = $row->filterColumns($filters);
            $mail->put('user', $user);
            $mails->put($mail->get('id'), $mail);
        }

        if ($mails->isEmpty()) {
            return new Collection();
        }

        // Get all recipients
        $recipientQuery = new RecipientDatabaseQuery($this->db);
        $recipientQuery->filterMail(...$mails->keys());
        $mailRecipients = $recipientQuery->execute();
        $mails->each(
            fn ($mail) => $mail->put(
                'recipients',
                $mailRecipients->get($mail->get('id'), new Collection())
            )
        );

        return $mails;
    }
}

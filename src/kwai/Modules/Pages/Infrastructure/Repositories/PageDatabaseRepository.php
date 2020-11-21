<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Infrastructure\Mappers\PageMapper;
use Kwai\Modules\Pages\Infrastructure\Tables;
use Kwai\Modules\Pages\Repositories\PageQuery;
use Kwai\Modules\Pages\Repositories\PageRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class PageDatabaseRepository
 */
class PageDatabaseRepository extends DatabaseRepository implements PageRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery();
        $query->filterId($id);

        try {
            $entities = $query->execute();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if (count($entities) ==  1) {
            return $entities[$id];
        }
        throw new PageNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): PageQuery
    {
        return new PageDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function create(Page $page): Entity
    {
        $data = PageMapper::toPersistence($page);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::PAGES())
            ->columns(
                ... array_keys($data)
            )
            ->values(
                ... array_values($data)
            )
        ;

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $pageId = $this->db->lastInsertId();

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::CONTENTS())
            ->columns(
                'page_id',
                'locale',
                'format',
                'title',
                'content',
                'summary',
                'user_id'
            )
        ;
        foreach ($page->getContents() as $content) {
            $query->values(
                $pageId,
                (string) $content->getLocale(),
                (string) $content->getFormat(),
                $content->getTitle(),
                $content->getContent(),
                $content->getSummary(),
                $content->getAuthor()->getId()
            );
        }

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return new Entity($pageId, $page);
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $page): void
    {
        $query = $this->db->createQueryFactory()
            ->update((string) Tables::PAGES())
            ->set(PageMapper::toPersistence($page->domain()))
            ->where(field('id')->eq($page->id()))
        ;

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        foreach ($page->getContents() as $content) {
            $query = $this->db->createQueryFactory()
                ->update((string) Tables::CONTENTS())
                ->set([
                    'format' => $content->getFormat(),
                    'title' => $content->getTitle(),
                    'content' => $content->getContent(),
                    'summary' => $content->getSummary(),
                    'user_id' => $content->getAuthor()->getId(),
                    'updated_at' => (string) Timestamp::createNow()
                ])
                ->where(
                    field('page_id')->eq($page->id())
                        ->and(field('locale')->eq($content->getLocale()))
                )
            ;
            try {
                $this->db->execute($query);
            } catch (QueryException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function remove(Entity $page): void
    {
        $query = $this->db->createQueryFactory()
            ->delete((string) Tables::CONTENTS())
            ->where(field('page_id')->eq($page->id()))
        ;

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $query = $this->db->createQueryFactory()
            ->delete((string) Tables::PAGES())
            ->where(field('id')->eq($page->id()))
        ;

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}

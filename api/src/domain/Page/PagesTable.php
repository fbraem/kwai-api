<?php

namespace Domain\Page;

use \Zend\Db\Sql\Expression;
use \Zend\Db\TableGateway\TableGateway;

class PagesTable implements PagesInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;

        $this->table = new TableGateway('pages', $this->db);
        $this->select = $this->createSelect();
    }

    private function createSelect()
    {
        $select = $this->table->getSql()->select();
        $select->columns([
            'page_id' => 'id',
            'page_enabled' => 'enabled',
            'page_priority' => 'priority',
            'page_remark' => 'remark',
            'page_category_id' => 'category_id',
            'page_created_at' => 'created_at',
            'page_updated_at' => 'updated_at'
        ]);

        $select->join(
            'page_contents',
            'pages.id = page_contents.page_id',
            null,
            $select::JOIN_LEFT
        );

        $select->join(
            'contents',
            'page_contents.content_id = contents.id',
            [
                'content_id' => 'id',
                'content_locale' => 'locale',
                'content_format' => 'format',
                'content_title' => 'title',
                'content_content' => 'content',
                'content_summary' => 'summary',
                'content_user_id' => 'user_id',
                'content_created_at' => 'created_at',
                'content_updated_at' => 'updated_at'
            ],
            $select::JOIN_LEFT
        );
        return $select;
    }

    public function whereCategory($id)
    {
        $this->select->where(['pages.category_id' => $id]);
        return $this;
    }

    public function whereAllowedToSee()
    {
        $this->select->where
            ->equalTo('pages.enabled', 1);
    }

    public function whereId($id)
    {
        $this->select->where(['pages.id' => $id]);
        return $this;
    }

    public function whereUser($id)
    {
        $this->select->where(['contents.user_id' => $id]);
    }

    public function orderByDate()
    {
        $this->select->order('pages.created_at ASC');
        return $this;
    }

    public function orderByPriority()
    {
        $this->select->order('pages.priority DESC');
        return $this;
    }

    public function orderByTitle()
    {
        $this->select->order('contents.title ASC');
        return $this;
    }

    public function findOne() : PageInterface
    {
        $result = $this->find();
        if ($result && count($result) > 0) {
            return reset($result);
        }
        throw new \Domain\NotFoundException("Page not found");
    }

    public function find(?int $limit = null, ?int $offset = null) : iterable
    {
        //TODO: for now we assume only 'nl'
        // In the future we must allow multiple locales
        $this->select->where(['contents.locale' => 'nl']);

        if ($limit) {
            $this->select->limit($limit);
        }
        if ($offset) {
            $this->select->offset($offset);
        }

        $pages = [];
        $categories = [];
        $users = [];
        $contents = [];

        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $page = self::getPrefixedValues($row, 'page_');
                $categories[$page['category_id']] = 1;
                $content = self::getPrefixedValues($row, 'content_');

                $users[$content['user_id']] = 1;

                if (! isset($contents[$page['id']])) {
                    $contents[$page['id']] = [];
                }
                $contents[$page['id']][] = $content;

                $pages[$page['id']] = $page;
            }
        }

        if (count($categories) > 0) {
            $categories = (new \Domain\Category\CategoriesTable($this->db))->whereId(array_keys($categories))->find();
        }

        if (count($users) > 0) {
            $users = (new \Domain\User\UsersTable($this->db))->whereIds(array_keys($users))->find();
        }

        $result = [];
        foreach ($pages as $page) {
            $page['contents'] = new PageContent($this->db, $page['id']);
            if (isset($contents[$page['id']])) {
                foreach ($contents[$page['id']] as $key => $content) {
                    $content['user'] = $users[$content['user_id']] ?? null;
                    $page['contents']->add(new \Domain\Content\Content($this->db, $content));
                }
            }
            $page['category'] = $categories[$page['category_id']] ?? null;
            $result[] = new Page($this->db, $page);
        }
        return $result;
    }

    public function count() : int
    {
        $select = clone $this->select;
        $select->columns(['c' => new Expression('COUNT(0)')]);
        $resultSet = $this->table->selectWith($select);
        return (int) $resultSet->current()->c;
    }

    private static function getPrefixedValues($arr, $prefix)
    {
        $length = strlen($prefix);
        $result = array_filter(
            (array) $arr,
            function ($val, $key) use ($length, $prefix) {
                return substr($key, 0, $length) == $prefix;
            },
            ARRAY_FILTER_USE_BOTH
        );
        foreach ($result as $key => $value) {
            $result[substr($key, $length)] = $value;
            unset($result[$key]);
        }
        return $result;
    }
}

<?php

namespace Domain\News;

use \Zend\Db\Sql\Expression;
use \Zend\Db\TableGateway\TableGateway;

class NewsStoriesTable implements NewsStoriesInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;

        $this->table = new TableGateway('news_stories', $this->db);
        $this->select = $this->createSelect();
    }

    private function createSelect()
    {
        $select = $this->table->getSql()->select();

        $select->columns([
            'news_id' => 'id',
            'news_enabled' => 'enabled',
            'news_featured' => 'featured',
            'news_featured_end_date' => 'featured_end_date',
            'news_featured_end_date_timezone' => 'featured_end_date_timezone',
            'news_publish_date' => 'publish_date',
            'news_publish_date_timezone' => 'publish_date_timezone',
            'news_end_date' => 'end_date',
            'news_end_date_timezone' => 'end_date_timezone',
            'news_remark' => 'remark',
            'news_category_id' => 'category_id',
            'news_user_id' => 'user_id',
            'news_created_at' => 'created_at',
            'news_updated_at' => 'updated_at'
        ]);

        $select->join(
            'news_contents',
            'news_stories.id = news_contents.news_id',
            [],
            $select::JOIN_LEFT
        );
        $select->join(
            'contents',
            'news_contents.content_id = contents.id',
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
        $this->select->where(['news_stories.category_id' => $id]);
        return $this;
    }

    public function whereFeatured()
    {
        $this->select->order('news_stories.featured DESC');
        $this->select->where
            ->greaterThan('news_stories.featured', '0')
            ->nest()
            ->isNull('news_stories.featured_end_date')
            ->or
            ->greaterThan('news_stories.featured_end_date', \Carbon\Carbon::now('UTC')->toDateTimeString())
            ->unnest();
        return $this;
    }

    public function whereAllowedToSee()
    {
        $this->select->where
            ->equalTo('news_stories.enabled', 1)
            ->nest()
            ->isNull('news_stories.publish_date')
            ->or
            ->lessThanOrEqualTo('news_stories.publish_date', \Carbon\Carbon::now('UTC')->toDateTimeString())
            ->unnest();
    }

    public function whereNotEnded()
    {
        $this->select->where
            ->nest()
            ->isNull('news_stories.end_date')
            ->or
            ->greaterThan('news_stories.end_date', \Carbon\Carbon::now('UTC')->toDateTimeString())
            ->unnest();
    }

    public function wherePublishedYear(int $year)
    {
        $this->select->where->expression('YEAR(news_stories.publish_date) = ?', $year);
        return $this;
    }

    public function wherePublishedYearMonth(int $year, int $month)
    {
        $this->wherePublishedYear($year);
        $this->select->where->expression('MONTH(news_stories.publish_date) = ?', $month);
        return $this;
    }

    public function whereUser($id)
    {
        $this->select->where(['contents.user_id' => $id]);
    }

    public function whereId($id)
    {
        $this->select->where(['news_stories.id' => $id]);
        return $this;
    }

    public function orderByDate()
    {
        $this->select->order('news_stories.publish_date DESC');
        return $this;
    }

    public function findOne() : NewsStoryInterface
    {
        $stories = $this->find();
        if ($stories && count($stories) > 0) {
            return reset($stories);
        }
        throw new \Domain\NotFoundException("News story not found");
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

        $stories = [];
        $categories = [];
        $users = [];
        $contents = [];

        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $story = self::getPrefixedValues($row, 'news_');
                $categories[$story['category_id']] = 1;
                $content = self::getPrefixedValues($row, 'content_');

                $users[$content['user_id']] = 1;
                if (! isset($contents[$story['id']])) {
                    $contents[$story['id']] = [];
                }
                $contents[$story['id']][] = $content;

                $stories[$story['id']] = $story;
            }
        }

        if (count($categories) > 0) {
            $categories = (new \Domain\Category\CategoriesTable($this->db))->whereId(array_keys($categories))->find();
        }

        if (count($users) > 0) {
            $users = (new \Domain\User\UsersTable($this->db))->whereIds(array_keys($users))->find();
        }

        $result = [];
        foreach ($stories as $story) {
            $story['contents'] = new NewsContent($this->db, $story['id']);
            if (isset($contents[$story['id']])) {
                foreach ($contents[$story['id']] as $key => $content) {
                    $content['user'] = $users[$content['user_id']] ?? null;
                    $story['contents']->add(new \Domain\Content\Content($this->db, $content));
                }
            }
            $story['category'] = $categories[$story['category_id']] ?? null;
            $result[] = new NewsStory($this->db, $story);
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

    public function archive() : iterable
    {
        $archive = $this->table->getSql()->select();
        $archive->columns([
            'year' => new Expression('YEAR(publish_date)'),
            'month' => new Expression('MONTH(publish_date)'),
            'count' => new Expression('COUNT(id)')
        ]);
        $archive->where->equalTo('enabled', 1);
        $archive->where
            ->nest()
            ->isNull('end_date')
            ->or
            ->greaterThan('end_date', \Carbon\Carbon::now('UTC')->toDateTimeString())
            ->unnest()
            ->nest()
            ->isNull('publish_date')
            ->or
            ->lessThanOrEqualTo('publish_date', \Carbon\Carbon::now('UTC')->toDateTimeString())
            ->unnest();
        $archive->group(['year', 'month']);
        $archive->order(['year DESC', 'month DESC']);

        $result = [];
        $resultSet = $this->table->selectWith($archive);
        foreach ($resultSet as $row) {
            $result[] = $row;
        }
        return $result;
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

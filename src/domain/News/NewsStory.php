<?php

namespace Domain\News;

class NewsStory extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [ 'category', 'category_id' ];

    public function _getFeaturedEndDate($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, 'UTC');
            return $date->toDateTimeString();
        }
        return null;
    }

    public function _getPublishDate($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, 'UTC');
            return $date->toDateTimeString();
        }
        return null;
    }

    public function _getEndDate($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, 'UTC');
            return $date->toDateTimeString();
        }
        return null;
    }
}

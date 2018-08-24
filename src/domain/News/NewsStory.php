<?php

namespace Domain\News;

class NewsStory extends \Cake\ORM\Entity
{
    protected $_hidden = [ 'contents', 'category', 'category_id' ];

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

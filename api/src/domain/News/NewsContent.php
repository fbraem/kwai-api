<?php

namespace Domain\News;

class NewsContent implements NewsContentInterface
{
    private $db;

    private $newsId;

    private $contents;

    public function __construct($db, int $newsId)
    {
        $this->db = $db;
        $this->newsId = $newsId;
        $this->contents = [];
    }

    public function newsId() : int
    {
        return $this->newsId;
    }

    public function add(\Domain\Content\Content $content)
    {
        $this->contents[] = $content;
    }

    public function replace(\Domain\Content\Content $content)
    {
        // For the moment we only support one language = one content / news story
        $this->contents = [];
        $this->add($content);
    }

    public function contents() : iterable
    {
        return $this->contents;
    }

    public function store()
    {
        $contentablesTable = new \Zend\Db\TableGateway\TableGateway('contentables', $this->db);
        foreach ($this->contents as $content) {
            $content->store();
            $contentablesTable->insert([
                'content_id' => $content->id(),
                'contentable_id' => $this->newsId,
                'contentable_type' => 'news'
            ]);
        }
    }

    public function delete()
    {
        $contentablesTable = new \Zend\Db\TableGateway\TableGateway('contentables', $this->db);
        foreach ($this->contents as $content) {
            $contentablesTable->delete([
                'content_id' => $content->id(),
                'contentable_id' => $this->newsId,
            ]);
            $content->delete();
        }
    }
}

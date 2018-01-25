<?php

namespace Domain\Page;

class PageContent implements PageContentInterface
{
    private $db;

    private $pageId;

    private $contents;

    public function __construct($db, int $pageId)
    {
        $this->db = $db;
        $this->pageId = $pageId;
        $this->contents = [];
    }

    public function pageId() : int
    {
        return $this->pageId;
    }

    public function add(\Domain\Content\Content $content)
    {
        $this->contents[] = $content;
    }

    public function replace(\Domain\Content\Content $content)
    {
        // For the moment we only support one language = one content / page
        $this->contents = [];
        $this->add($content);
    }

    public function contents() : iterable
    {
        return $this->contents;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('page_contents', $this->db);
        foreach ($this->contents as $content) {
            $content->store();
            $table->insert([
                'content_id' => $content->id(),
                'page_id' => $this->pageId
            ]);
        }
    }

    public function delete()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('page_contents', $this->db);
        foreach ($this->contents as $content) {
            $table->delete([
                'content_id' => $content->id(),
                'page_id' => $this->pageId,
            ]);
            $content->delete();
        }
    }
}

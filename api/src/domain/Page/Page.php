<?php

namespace Domain\Page;

class Page implements PageInterface
{
    private $db;

    private $id;
    private $enabled;
    private $priority;
    private $remark;
    private $contents;
    private $category;

    use \Domain\DatetimeMetaTrait;

    public function __construct($db, ?iterable $data = null)
    {
        $this->db = $db;

        if ($data) {
            $this->hydrate($data);
        }
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'] ?? null;
        $this->enabled = $data['enabled'] ?? 0;
        $this->priority = $data['priority'] ?? 0;
        $this->remark = $data['remark'] ?? null;
        $this->contents = $data['contents'] ?? null;
        $this->category = $data['category'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'enabled' => $this->enabled(),
            'priority' => $this->priority(),
            'remark' => $this->remark(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('pages', $this->db);

        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->category) {
            $data['category_id'] = $this->category->id();
        }

        if ($this->id()) {
            $table->update($data, [
                'id' => $this->id()
            ]);
        } else {
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
            $this->contents = new PageContent($this->db, $this->id);
        }
    }

    public function delete()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('pages', $this->db);
        $this->contents->delete();
        $table->delete(['id' => $this->id()]);
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function enabled() : bool
    {
        return $this->enabled;
    }

    public function remark() : ?string
    {
        return $this->remark;
    }

    public function contents() : ?PageContent
    {
        return $this->contents;
    }

    public function category()
    {
        return $this->category;
    }

    public function priority() : ?int
    {
        return $this->priority;
    }
}

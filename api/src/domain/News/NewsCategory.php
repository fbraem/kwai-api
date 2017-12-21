<?php

namespace Domain\News;

class NewsCategory implements NewsCategoryInterface
{
    private $db;

    private $id;
    private $name;
    private $description;
    private $remark;
    private $user;

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
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->remark = $data['remark'] ?? null;
        $this->user = $data['user'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'description' => $this->description(),
            'remark' => $this->remark(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('news_categories', $this->db);

        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->user) {
            $data['user_id'] = $this->user->id();
        }
        if ($this->id()) {
            $table->update($data, ['id' => $this->id()]);
        } else {
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
        }
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function description() : string
    {
        return $this->description;
    }

    public function remark() : ?string
    {
        return $this->remark;
    }

    public function user()
    {
        return $this->user;
    }
}

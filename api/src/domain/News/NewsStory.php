<?php

namespace Domain\News;

class NewsStory implements NewsStoryInterface
{
    private $db;

    private $id;
    private $enabled;
    private $featured;
    private $featuredEndDate;
    private $publishDate;
    private $endDate;
    private $remark;
    private $contents;
    private $category;
    private $author;

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
        $this->featured = $data['featured'] ?? 0;
        $this->featuredEndDate = $data['featured_end_date'] ?? null;
        $this->publishDate = $data['publish_date'] ?? null;
        $this->endDate = $data['end_date'] ?? null;
        $this->remark = $data['remark'] ?? null;
        $this->contents = $data['contents'] ?? null;
        $this->category = $data['category'] ?? null;
        $this->author = $data['author'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'publish_date' => $this->publishDate(),
            'enabled' => $this->enabled(),
            'featured' => $this->featured(),
            'featured_end_date' => $this->featuredEndDate(),
            'end_date' => $this->endDate(),
            'remark' => $this->remark(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('news_stories', $this->db);

        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->category) {
            $data['category_id'] = $this->category->id();
        }
        if ($this->author) {
            $data['user_id'] = $this->author->id();
        }

        if ($this->id()) {
            $table->update($data, [
                'id' => $this->id()
            ]);
        } else {
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
            $this->contents = new NewsContent($this->db, $this->id);
        }
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function enabled() : bool
    {
        return $this->enabled;
    }

    public function featured() : int
    {
        return $this->featured;
    }

    public function featuredEndDate() : ?string
    {
        return $this->featuredEndDate;
    }

    public function publishDate() : ?string
    {
        return $this->publishDate;
    }

    public function endDate() : ?string
    {
        return $this->endDate;
    }

    public function remark() : ?string
    {
        return $this->remark;
    }

    public function contents() : ?NewsContent
    {
        return $this->contents;
    }

    public function category()
    {
        return $this->category;
    }

    public function author()
    {
        return $this->author;
    }
}

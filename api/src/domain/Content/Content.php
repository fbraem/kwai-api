<?php

namespace Domain\Content;

class Content implements ContentInterface
{
    use \Domain\DatetimeMetaTrait;

    private $db;

    private $id;
    private $locale;
    private $format;
    private $title;
    private $summary;
    private $content;
    private $user;

    public function __construct($db, ?iterable $data)
    {
        $this->db = $db;
        if ($data) {
            $this->hydrate($data);
        }
    }

    public function extract() : iterable
    {
        return [
            'id' => (int) $this->id(),
            'locale' => $this->locale(),
            'format' => $this->format(),
            'title' => $this->title(),
            'summary' => $this->summary(),
            'content' => $this->content(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'] ?? null;
        $this->locale = $data['locale'];
        $this->format = $data['format'];
        $this->title = $data['title'];
        $this->summary = $data['summary'];
        $this->content = $data['content'];
        $this->user = $data['user'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('contents', $this->db);

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

    public function delete()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('contents', $this->db);
        $table->delete(['id' => $this->id()]);
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function locale() : string
    {
        return $this->locale;
    }

    public function format() : string
    {
        return $this->format;
    }

    public function title() : string
    {
        return $this->title;
    }

    public function summary() : string
    {
        return $this->summary;
    }

    public function content() : string
    {
        return $this->content;
    }

    public function user() : \Domain\User\UserInterface
    {
        return $this->user;
    }
}

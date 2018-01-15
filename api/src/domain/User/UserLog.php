<?php
namespace Domain\User;

/**
 * @inheritdoc
 */
class UserLog implements UserLogInterface
{
    private $db;

    private $id;
    private $user;
    private $action;
    private $rest;
    private $modelId;

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
        $this->user = $data['user'] ?? null;
        $this->action = $data['action'] ?? null;
        $this->rest = $data['rest'] ?? null;
        $this->modelId = $data['model_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'action' => $this->action(),
            'rest' => $this->rest(),
            'model_id' => $this->modelId(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('user_logs', $this->db);

        $data = $this->extract();
        if ($this->user) {
            $data['user_id'] = $this->user->id();
        }

        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
            $table->update($data, $this->id());
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
        }
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function action() : string
    {
        return $this->action;
    }

    public function rest() : string
    {
        return $this->rest;
    }

    public function modelId() : int
    {
        return $this->modelId;
    }

    public function user() : UserInterface
    {
        return $this->user;
    }
}

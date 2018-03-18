<?php

namespace Domain;

trait DatetimeMetaTrait
{
    private $created_at;

    private $updated_at;

    public function createdAt() : ?string
    {
        if ($this->created_at) {
            $createdAt = new \Carbon\Carbon($this->created_at);
            return $createdAt->toDateTimeString();
        }
        return null;
    }

    public function updatedAt() : ?string
    {
        if ($this->updated_at) {
            $updatedAt = new \Carbon\Carbon($this->updated_at);
            return $updatedAt->toDateTimeString();
        }
        return null;
    }

    public function _getCreatedAt($value)
    {
        if ($value) {
            return (new \Carbon\Carbon($value))->toDateTimeString();
        }
        return null;
    }

    public function _getUpdatedAt($value)
    {
        if ($value) {
            return (new \Carbon\Carbon($value))->toDateTimeString();
        }
        return null;
    }
}

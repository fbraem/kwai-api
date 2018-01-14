<?php

namespace Domain;

trait DatetimeMetaTrait
{
    private $created_at;

    private $updated_at;

    public function createdAt() : ?string
    {
        $createdAt = new \Carbon\Carbon($this->created_at);
        return $createdAt->toDateTimeString();
    }

    public function updatedAt() : ?string
    {
        if ($this->updated_at) {
            $updatedAt = new \Carbon\Carbon($this->updated_at);
            return $updatedAt->toDateTimeString();
        }
        return null;
    }
}

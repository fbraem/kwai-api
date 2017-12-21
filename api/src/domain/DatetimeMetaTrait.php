<?php

namespace Domain;

trait DatetimeMetaTrait
{
    private $created_at;

    private $updated_at;

    public function createdAt() : ?string
    {
        return $this->created_at;
    }

    public function updatedAt() : ?string
    {
        return $this->updated_at;
    }
}

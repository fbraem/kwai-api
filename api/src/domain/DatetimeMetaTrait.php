<?php

namespace Domain;

trait DatetimeMetaTrait
{
    private $created_at;

    private $updated_at;

    public function createdAt() : ?string
    {
        // Timestamps are always converted by MySQL to server time
        // We need UTC, so convert it back ...
        $createdAt = new \Carbon\Carbon($this->created_at);
        $createdAt->timezone('UTC');
        return $createdAt->toDateTimeString();
    }

    public function updatedAt() : ?string
    {
        if ($this->updated_at) {
            // Timestamps are always converted by MySQL to server time
            // We need UTC, so convert it back ...
            $updatedAt = new \Carbon\Carbon($this->updated_at);
            $updatedAt->timezone('UTC');
            return $updatedAt->toDateTimeString();
        }
        return null;
    }
}

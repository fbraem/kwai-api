<?php

namespace Domain\News;

interface NewsContentInterface
{
    public function newsId() : int;
    public function contents() : iterable;
    public function add(\Domain\Content\Content $content);

    public function store();
    public function delete();
}

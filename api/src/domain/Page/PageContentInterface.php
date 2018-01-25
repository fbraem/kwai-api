<?php

namespace Domain\Page;

interface PageContentInterface
{
    public function pageId() : int;
    public function contents() : iterable;
    public function add(\Domain\Content\Content $content);

    public function store();
    public function delete();
}

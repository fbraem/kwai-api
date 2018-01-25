<?php
namespace Domain\Page;

interface PageInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function enabled() : bool;
    public function remark() : ?string;
    public function contents() : ?PageContent;
    public function category();

    public function store();
    public function delete();
}

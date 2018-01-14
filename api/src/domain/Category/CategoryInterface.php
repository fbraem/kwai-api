<?php
namespace Domain\Category;

interface CategoryInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function name() : string;
    public function description() : string;
    public function remark() : ?string;
    public function user();

    public function store();
}

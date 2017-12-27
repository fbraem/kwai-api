<?php
namespace Domain\Content;

interface ContentInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function locale() : string;
    public function format() : string;
    public function title() : string;
    public function summary() : string;
    public function content() : string;

    public function store();
    public function delete();
}

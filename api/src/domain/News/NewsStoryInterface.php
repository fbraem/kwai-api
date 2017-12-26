<?php
namespace Domain\News;

interface NewsStoryInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function enabled() : bool;
    public function featured() : int;
    public function featuredEndDate() : ?string;
    public function featuredEndDateTimezone() : ?string;
    public function publishDate() : ?string;
    public function publishDateTimezone() : ?string;
    public function endDate() : ?string;
    public function endDateTimezone() : ?string;
    public function remark() : ?string;
    public function contents() : ?NewsContent;
    public function category();
    public function author();

    public function store();
    public function delete();
}

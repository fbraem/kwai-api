<?php

namespace Judo\Domain\Member;

interface MemberInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    //public function license() : ?string;
    //public function competition() : ?bool;
    //public function remark() : ?string;
    public function person() : ?\Domain\Person\PersonInterface;
    public function grades() : ?iterable;

    public function store();
    public function delete();
}

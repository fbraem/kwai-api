<?php

namespace Judo\Domain\Member;

interface GradeInterface extends \Domain\HydratorInterface
{
    public function id() : ?int;
    /*
        public function grade() : ?string;
        public function name() : ?string;
        public function color() : ?string;
        public function position() : ?int;
        public function min_age() : ?int;
        public function prepare_time() : ?int;
    */
}

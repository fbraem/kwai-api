<?php

namespace Domain\Content;

interface ContentsInterface
{
    function find() : iterable;
    function count() : int;
}

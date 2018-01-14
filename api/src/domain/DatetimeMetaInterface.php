<?php

namespace Domain;

interface DatetimeMetaInterface
{
    function createdAt() : ?string;
    function updatedAt() : ?string;
}

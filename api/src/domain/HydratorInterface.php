<?php

namespace Domain;

interface HydratorInterface
{
    function extract() : iterable;
    function hydrate(iterable $data);
}

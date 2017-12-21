<?php

namespace Domain;

class DomainHydrator implements \Zend\Hydrator\HydratorInterface
{
    public function extract(HydratorInterface $object)
    {
        return $object->extract();
    }

    public function hydrate(array $data, HydratorInterface $object)
    {
        return $object->hydrate($data);
    }
}

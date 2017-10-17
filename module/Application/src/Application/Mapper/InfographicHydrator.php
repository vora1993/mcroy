<?php

namespace Application\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use Application\Entity\InfographicInterface as InfographicEntityInterface;

class InfographicHydrator extends ClassMethods
{

    public function extract($object)
    {
        if (!$object instanceof InfographicEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of Application\Entity\InfographicInterface');
        }
        $data = parent::extract($object);
        unset($data['array_copy']);
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        if (!$object instanceof InfographicEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of Application\Entity\InfographicInterface');
        }
        return parent::hydrate($data, $object);
    }

    protected function mapField($keyFrom, $keyTo, array $array)
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);
        return $array;
    }
}

<?php

namespace Application\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use Application\Entity\PersonalLoanInterface as PersonalLoanEntityInterface;

class PersonalLoanHydrator extends ClassMethods
{

    public function extract($object)
    {
        if (!$object instanceof PersonalLoanEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of Application\Entity\PersonalLoanInterface');
        }
        $data = parent::extract($object);
        unset($data['array_copy']);
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        if (!$object instanceof PersonalLoanEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of Application\Entity\PersonalLoanInterface');
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

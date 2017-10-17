<?php

namespace Application\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use Application\Entity\PropertyLoanPackageInterface as PropertyLoanPackageEntityInterface;

class PropertyLoanPackageHydrator extends ClassMethods
{

    public function extract($object)
    {
        if (!$object instanceof PropertyLoanPackageEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of Application\Entity\PropertyLoanPackageInterface');
        }
        $data = parent::extract($object);
        unset($data['array_copy']);
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        if (!$object instanceof PropertyLoanPackageEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of Application\Entity\PropertyLoanPackageInterface');
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

<?php

namespace {$module}\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use {$module}\Entity\{$module}Interface as {$module}EntityInterface;

class {$module}Hydrator extends ClassMethods
{

    public function extract($object)
    {
        if (!$object instanceof {$module}EntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of {$module}\Entity\{$module}Interface');
        }
        $data = parent::extract($object);
        unset($data['array_copy']);
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        if (!$object instanceof {$module}EntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of {$module}\Entity\{$module}Interface');
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

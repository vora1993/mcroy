<?php

namespace Application\Mapper;

interface SliderInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($slider);

    public function update($slider);
}
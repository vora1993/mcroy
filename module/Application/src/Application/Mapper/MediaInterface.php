<?php

namespace Application\Mapper;

interface MediaInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($media);

    public function update($media);
}
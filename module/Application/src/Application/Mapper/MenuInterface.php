<?php

namespace Application\Mapper;

interface MenuInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($menu);

    public function update($menu);
}
<?php

namespace Application\Mapper;

interface MenuGroupInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($menu_group);

    public function update($menu_group);
}
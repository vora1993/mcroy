<?php

namespace Application\Mapper;

interface RoleInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($role);

    public function update($role);
    
    public function delete($role);
}
<?php

namespace Application\Mapper;

interface SubscribeInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($subscribe);

    public function update($subscribe);
    
    public function delete($subscribe);
}
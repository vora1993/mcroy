<?php

namespace Application\Mapper;

interface PostInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($post);

    public function update($post);
    
    public function delete($post);
}
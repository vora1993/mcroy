<?php

namespace Application\Mapper;

interface PageInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($page);

    public function update($page);
    
    public function delete($page);
}
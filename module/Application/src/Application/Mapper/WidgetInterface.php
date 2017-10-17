<?php

namespace Application\Mapper;

interface WidgetInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($widget);

    public function update($widget);
    
    public function delete($widget);
}
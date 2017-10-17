<?php

namespace Application\Mapper;

interface MediaImportInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($media);

    public function update($media);
}
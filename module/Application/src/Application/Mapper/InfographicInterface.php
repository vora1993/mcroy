<?php

namespace Application\Mapper;

interface InfographicInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($infographic);

    public function update($infographic);
    
    public function delete($infographic);
}
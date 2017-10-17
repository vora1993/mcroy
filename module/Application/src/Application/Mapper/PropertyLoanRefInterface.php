<?php

namespace Application\Mapper;

interface PropertyLoanRefInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($loan);

    public function update($loan);
    
    public function delete($loan);
}
<?php

namespace Application\Mapper;

interface TooltipPropertyLoanInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($property_loan);

    public function update($property_loan);
    
    public function delete($property_loan);
}
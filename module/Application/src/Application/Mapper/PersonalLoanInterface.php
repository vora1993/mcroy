<?php

namespace Application\Mapper;

interface PersonalLoanInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($personal_loan);

    public function update($personal_loan);
    
    public function delete($personal_loan);
}
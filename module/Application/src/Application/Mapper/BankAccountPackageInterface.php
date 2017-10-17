<?php

namespace Application\Mapper;

interface BankAccountPackageInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($bank_account);

    public function update($bank_account);
    
    public function delete($bank_account);
}
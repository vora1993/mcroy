<?php

namespace Application\Mapper;

interface BankInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($bank);

    public function update($bank);
    
    public function delete($bank);
}
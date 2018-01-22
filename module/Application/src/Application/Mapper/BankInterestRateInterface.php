<?php

namespace Application\Mapper;

interface BankInterestRateInterface
{
    public function fetchAll($condition);

    public function fetchRow($condition);

    public function insert($category);

    public function update($category);
}
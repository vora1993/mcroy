<?php

namespace Application\Mapper;

interface ReferralInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);

    public function insert($user);

    public function update($user);
}

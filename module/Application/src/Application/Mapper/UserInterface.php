<?php

namespace Application\Mapper;

interface UserInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function findByEmail($email);

    public function findByUsername($username);

    public function findById($id);

    public function insert($user);

    public function update($user);
}

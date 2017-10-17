<?php

namespace {$module}\Mapper;

interface {$module}Interface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($user);

    public function update($user);
}
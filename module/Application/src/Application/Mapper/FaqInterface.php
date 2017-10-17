<?php

namespace Application\Mapper;

interface FaqInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($faq);

    public function update($faq);
    
    public function delete($faq);
}
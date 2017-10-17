<?php

namespace Application\Mapper;

interface TestimonialInterface
{
    public function fetchAll($condition);
    
    public function fetchRow($condition);
    
    public function insert($testimonial);

    public function update($testimonial);
}
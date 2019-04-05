<?php
namespace Application\Entity;

class Design implements DesignInterface
{
	protected $id;
    protected $feautures;
    protected $testimonials_title;
    protected $testimonials_sign;
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    public function getFeautures()
    {
        return $this->feautures;
    }
    public function setFeautures($feauture)
    {
        $this->feautures = $feauture;
        return $this;
    }
    
    public function getTestimonialsTitle()
    {
        return $this->testimonials_title;
    }
    public function setTestimonialsTitle($name)
    {
        $this->testimonials_title = $name;
        return $this;
    }
    
    public function getTestimonialsSign()
    {
        return $this->testimonials_sign;
    }
    public function setTestimonialsSign($name)
    {
        $this->testimonials_sign = $name;
        return $this;
    }
}
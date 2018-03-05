<?php
namespace Application\Entity;

class TooltipPropertyLoan implements TooltipPropertyLoanInterface
{
	protected $id;
    protected $name;
    protected $value;
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
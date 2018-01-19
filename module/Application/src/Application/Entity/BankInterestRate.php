<?php
namespace Application\Entity;

class BankInterestRate implements BankInterestRateInterface
{
	protected $id;
    protected $bankid;
    protected $name;
    protected $rate;
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    public function getBankId()
    {
        return $this->bankid;
    }
    public function setBankId($id)
    {
        $this->bankid = (int) $id;
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
    
    public function getRate()
    {
        return $this->rate;
    }
    public function setRate($rate)
    {
        $this->rate = (float)$rate;
        return $this;
    }
}
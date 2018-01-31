<?php
namespace Application\Entity;

class BankInterestRate implements BankInterestRateInterface
{
	protected $id;
    protected $bankid;
    protected $name;
    protected $rate;
    protected $type;
    protected $status;
    protected $sort;
    protected $display;
    
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

    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = (string)$type;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = (int) $status;
        return $this;
    }

    public function getSort()
    {
        return $this->sort;
    }
    public function setSort($sort)
    {
        $this->sort = (int) $sort;
        return $this;
    }

    public function getDisplay()
    {
        return $this->display;
    }
    public function setDisplay($display)
    {
        $this->display = $display;
        return $this;
    }
}
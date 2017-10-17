<?php
namespace Application\Entity;

class PropertyLoanBank implements PropertyLoanBankInterface
{
	protected $id;
    protected $title;
    protected $bank_id;
    protected $promotions;
    protected $property;
    protected $type;
    protected $property_status;
    protected $package;
    protected $floating_type;
    protected $sibor;
    protected $variable;
    protected $sor;
    protected $lock_in_year;
    protected $int_year_1;
    protected $int_year_2;
    protected $int_year_3;
    protected $int_year_4;
    protected $remark;
    protected $date_added;
    protected $date_modified;
    protected $status;
    
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    public function getBankId()
    {
        return $this->bank_id;
    }
    public function setBankId($bank_id)
    {
        $this->bank_id = (int) $bank_id;
        return $this;
    }
    
    public function getPromotions()
    {
        return $this->promotions;
    }
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
        return $this;
    }
    
    public function getProperty()
    {
        return $this->property;
    }
    public function setProperty($property)
    {
        $this->property = $property;
        return $this;
    }
    
    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function getPropertyStatus()
    {
        return $this->property_status;
    }
    public function setPropertyStatus($property_status)
    {
        $this->property_status = $property_status;
        return $this;
    }
    
    public function getPackage()
    {
        return $this->package;
    }
    public function setPackage($package)
    {
        $this->package = $package;
        return $this;
    }
    
    public function getFloatingType()
    {
        return $this->floating_type;
    }
    public function setFloatingType($floating_type)
    {
        $this->floating_type = $floating_type;
        return $this;
    }
    
    public function getSibor()
    {
        return $this->sibor;
    }
    public function setSibor($sibor)
    {
        $this->sibor = $sibor;
        return $this;
    }
    
    public function getVariable()
    {
        return $this->variable;
    }
    public function setVariable($variable)
    {
        $this->variable = $variable;
        return $this;
    }
    
    public function getSor()
    {
        return $this->sor;
    }
    public function setSor($sor)
    {
        $this->sor = $sor;
        return $this;
    }
    
    public function getLockInYear()
    {
        return $this->lock_in_year;
    }
    public function setLockInYear($lock_in_year)
    {
        $this->lock_in_year = $lock_in_year;
        return $this;
    }
    
    public function getIntYear1()
    {
        return $this->int_year_1;
    }
    public function setIntYear1($int_year_1)
    {
        $this->int_year_1 = $int_year_1;
        return $this;
    }
    
    public function getIntYear2()
    {
        return $this->int_year_2;
    }
    public function setIntYear2($int_year_2)
    {
        $this->int_year_2 = $int_year_2;
        return $this;
    }
    
    public function getIntYear3()
    {
        return $this->int_year_3;
    }
    public function setIntYear3($int_year_3)
    {
        $this->int_year_3 = $int_year_3;
        return $this;
    }
    
    public function getIntYear4()
    {
        return $this->int_year_4;
    }
    public function setIntYear4($int_year_4)
    {
        $this->int_year_4 = $int_year_4;
        return $this;
    }
    
    public function getRemark()
    {
        return $this->remark;
    }
    public function setRemark($remark)
    {
        $this->remark = $remark;
        return $this;
    }
    
    public function getDateAdded()
    {
        return $this->date_added;
    }
    public function setDateAdded($date_added)
    {
        $this->date_added = $date_added;
        return $this;
    }
    
    public function getDateModified()
    {
        return $this->date_modified;
    }
    
    public function setDateModified($date_modified)
    {
        $this->date_modified = $date_modified;
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
}
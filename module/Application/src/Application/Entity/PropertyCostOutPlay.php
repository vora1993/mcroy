<?php
namespace Application\Entity;

class PropertyCostOutPlay implements PropertyCostOutPlayInterface
{
	protected $id;
    protected $mortgage_stamp_duty;
    protected $valuation_fee;
    protected $legal_fee;
    protected $fire_insurance;
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }
    
    public function getMortgageStampDuty()
    {
        return $this->mortgage_stamp_duty;
    }
    public function setMortgageStampDuty($value)
    {
        $this->mortgage_stamp_duty = $value;
        return $this;
    }
    
    public function getValuationFee()
    {
        return $this->valuation_fee;
    }
    public function setValuationFee($value)
    {
        $this->valuation_fee = $value;
        return $this;
    }

    public function getLegalFee()
    {
        return $this->legal_fee;
    }
    public function setLegalFee($value)
    {
        $this->legal_fee = $value;
        return $this;
    }

    public function getFireInsurance()
    {
        return $this->fire_insurance;
    }
    public function setFireInsurance($value)
    {
        $this->fire_insurance = $value;
        return $this;
    }
}
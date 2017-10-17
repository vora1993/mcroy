<?php
namespace Application\Entity;

class PropertyLoan implements PropertyLoanInterface
{
	protected $id;
    protected $user_id;
    protected $type;
    protected $category_id;
    protected $property_type;
    protected $project_name;
    protected $property_status;
    protected $option_fee;
    protected $offer_opts;
    protected $existing;
    protected $remark;
    protected $int_rate;
    protected $loan_amount;
    protected $loan_tenure;
    protected $loan_percent;
    protected $fixed_rates;
    protected $penalty_fee;
    protected $monthly_payment;
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
    
    public function getUserId()
    {
        return $this->user_id;
    }
    public function setUserId($user_id)
    {
        $this->user_id = (int) $user_id;
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
    
    public function getCategoryId()
    {
        return $this->category_id;
    }
    public function setCategoryId($category_id)
    {
        $this->category_id = (int) $category_id;
        return $this;
    }
    
    public function getPropertyType()
    {
        return $this->property_type;
    }
    public function setPropertyType($property_type)
    {
        $this->property_type = $property_type;
        return $this;
    }
    
    public function getProjectName()
    {
        return $this->project_name;
    }
    public function setProjectName($project_name)
    {
        $this->project_name = $project_name;
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
    
    public function getOptionFee()
    {
        return $this->option_fee;
    }
    public function setOptionFee($option_fee)
    {
        $this->option_fee = $option_fee;
        return $this;
    }
    
    public function getOfferOpts()
    {
        return $this->offer_opts;
    }
    public function setOfferOpts($offer_opts)
    {
        $this->offer_opts = $offer_opts;
        return $this;
    }
    
    public function getExisting()
    {
        return $this->existing;
    }
    public function setExisting($existing)
    {
        $this->existing = $existing;
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
    
    public function getIntRate()
    {
        return $this->int_rate;
    }
    public function setIntRate($int_rate)
    {
        $this->int_rate = $int_rate;
        return $this;
    }
    
    public function getLoanAmount()
    {
        return $this->loan_amount;
    }
    public function setLoanAmount($loan_amount)
    {
        $this->loan_amount = $loan_amount;
        return $this;
    }
    
    public function getLoanTenure()
    {
        return $this->loan_tenure;
    }
    public function setLoanTenure($loan_tenure)
    {
        $this->loan_tenure = $loan_tenure;
        return $this;
    }
    
    public function getLoanPercent()
    {
        return $this->loan_percent;
    }
    public function setLoanPercent($loan_percent)
    {
        $this->loan_percent = $loan_percent;
        return $this;
    }
    
    public function getFixedRates()
    {
        return $this->fixed_rates;
    }
    public function setFixedRates($fixed_rates)
    {
        $this->fixed_rates = $fixed_rates;
        return $this;
    }
    
    
    public function getMonthlyPayment()
    {
        return $this->monthly_payment;
    }
    public function setMonthlyPayment($monthly_payment)
    {
        $this->monthly_payment = $monthly_payment;
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
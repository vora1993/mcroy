<?php
namespace Application\Entity;

class Loan implements LoanInterface
{
	protected $id;
    protected $bank_id;
    protected $type;
    protected $category_id;
    protected $loan_title;
    protected $promotions;
    protected $benefit;
    protected $interest_rate;
    protected $max_loan_amount;
    protected $max_tenor;
    protected $processing_fee;
    protected $annual_fee;
    protected $penalty_fee;
    protected $lock_in_period;
    protected $min_sales_turnover;
    protected $min_years_of_incorporation;
    protected $url;
    protected $int_rate;
    protected $max_tenure;
    protected $max_loan_amt;
    protected $prepayment_penalty_fee;
    protected $restructuring_of_loan_tenor;
    protected $min_turnover;
    protected $min_years_incorporation;
    protected $min_age;
    protected $bankruptcy;
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
    
    public function getBankId()
    {
        return $this->bank_id;
    }
    public function setBankId($bank_id)
    {
        $this->bank_id = (int) $bank_id;
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
    
    public function getLoanTitle()
    {
        return $this->loan_title;
    }
    public function setLoanTitle($loan_title)
    {
        $this->loan_title = $loan_title;
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
    
    public function getBenefit()
    {
        return $this->benefit;
    }
    public function setBenefit($benefit)
    {
        $this->benefit = $benefit;
        return $this;
    }
    
    public function getInterestRate()
    {
        return $this->interest_rate;
    }
    public function setInterestRate($interest_rate)
    {
        $this->interest_rate = $interest_rate;
        return $this;
    }
    
    public function getMaxLoanAmount()
    {
        return $this->max_loan_amount;
    }
    public function setMaxLoanAmount($max_loan_amount)
    {
        $this->max_loan_amount = $max_loan_amount;
        return $this;
    }
    
    public function getMaxTenor()
    {
        return $this->max_tenor;
    }
    public function setMaxTenor($max_tenor)
    {
        $this->max_tenor = (int) $max_tenor;
        return $this;
    }
    
    public function getProcessingFee()
    {
        return $this->processing_fee;
    }
    public function setProcessingFee($processing_fee)
    {
        $this->processing_fee = $processing_fee;
        return $this;
    }
    
    public function getAnnualFee()
    {
        return $this->annual_fee;
    }
    public function setAnnualFee($annual_fee)
    {
        $this->annual_fee = $annual_fee;
        return $this;
    }
    
    public function getPenaltyFee()
    {
        return $this->penalty_fee;
    }
    public function setPenaltyFee($penalty_fee)
    {
        $this->penalty_fee = $penalty_fee;
        return $this;
    }
    
    public function getLockInPeriod()
    {
        return $this->lock_in_period;
    }
    public function setLockInPeriod($lock_in_period)
    {
        $this->lock_in_period = $lock_in_period;
        return $this;
    }
    
    public function getMinSalesTurnover()
    {
        return $this->min_sales_turnover;
    }
    public function setMinSalesTurnover($min_sales_turnover)
    {
        $this->min_sales_turnover = $min_sales_turnover;
        return $this;
    }
    
    public function getMinYearsOfIncorporation()
    {
        return $this->min_years_of_incorporation;
    }
    public function setMinYearsOfIncorporation($min_years_of_incorporation)
    {
        $this->min_years_of_incorporation = $min_years_of_incorporation;
        return $this;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
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
    
    
    public function getIntRate()
    {
        return $this->int_rate;
    }
    public function setIntRate($int_rate)
    {
        $this->int_rate = $int_rate;
        return $this;
    }
    
    public function getMaxTenure()
    {
        return $this->max_tenure;
    }
    public function setMaxTenure($max_tenure)
    {
        $this->max_tenure = (int) $max_tenure;
        return $this;
    }
    
    public function getMaxLoanAmt()
    {
        return $this->max_loan_amt;
    }
    public function setMaxLoanAmt($max_loan_amt)
    {
        $this->max_loan_amt = (int) $max_loan_amt;
        return $this;
    }
    
    public function getPrepaymentPenaltyFee()
    {
        return $this->prepayment_penalty_fee;
    }
    public function setPrepaymentPenaltyFee($prepayment_penalty_fee)
    {
        $this->prepayment_penalty_fee = $prepayment_penalty_fee;
        return $this;
    }
    
    public function getRestructuringOfLoanTenor()
    {
        return $this->restructuring_of_loan_tenor;
    }
    public function setRestructuringOfLoanTenor($restructuring_of_loan_tenor)
    {
        $this->restructuring_of_loan_tenor = $restructuring_of_loan_tenor;
        return $this;
    }
    
    public function getMinTurnover()
    {
        return $this->min_turnover;
    }
    public function setMinTurnover($min_turnover)
    {
        $this->min_turnover = (int) $min_turnover;
        return $this;
    }
    
    public function getMinYearsIncorporation()
    {
        return $this->min_years_incorporation;
    }
    public function setMinYearsIncorporation($min_years_incorporation)
    {
        $this->min_years_incorporation = (int) $min_years_incorporation;
        return $this;
    }
    
    public function getMinAge()
    {
        return $this->min_age;
    }
    public function setMinAge($min_age)
    {
        $this->min_age = $min_age;
        return $this;
    }
    
    public function getBankruptcy()
    {
        return $this->bankruptcy;
    }
    public function setBankruptcy($bankruptcy)
    {
        $this->bankruptcy = $bankruptcy;
        return $this;
    }
    
}
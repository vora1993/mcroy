<?php
namespace Application\Entity;

class PersonalLoan implements PersonalLoanInterface
{
	protected $id;
    protected $user_id;
    protected $loan_id;
    protected $type;
    protected $category_id;
    protected $name;
    protected $email;
    protected $phone;
    protected $company_name;
    protected $remark;
    protected $int_rate;
    protected $loan_amount;
    protected $loan_tenure;
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
    
    public function getLoanId()
    {
        return $this->loan_id;
    }
    public function setLoanId($loan_id)
    {
        $this->loan_id = (int) $loan_id;
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
    
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    public function getPhone()
    {
        return $this->phone;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }
    
    public function getCompanyName()
    {
        return $this->company_name;
    }
    public function setCompanyName($company_name)
    {
        $this->company_name = $company_name;
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
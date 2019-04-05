<?php
namespace Application\Entity;

class BankAccountPackage implements BankAccountPackageInterface
{
	protected $id;
    protected $bank_id;
    protected $category_id;
    protected $category_account;
    protected $loan_title;
    protected $promotions;
    protected $initial_deposit_amount;
    protected $interest_rate;
    protected $minimum_balance;
    protected $cheque_book_fees;
    protected $internet_banking_fees;
    protected $annual_fee;
    protected $service_fee;
    protected $highlight;
    protected $int_rate;
    protected $citizenship;
    protected $age;
    protected $tenor;
    protected $link;
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
    
    public function getCategoryId()
    {
        return $this->category_id;
    }
    public function setCategoryId($category_id)
    {
        $this->category_id = (int) $category_id;
        return $this;
    }

    public function getCategoryAccount()
    {
        return $this->category_account;
    }
    public function setCategoryAccount($category_account)
    {
        $this->category_account = (int) $category_account;
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
    
    public function getInitialDepositAmount()
    {
        return $this->initial_deposit_amount;
    }
    public function setInitialDepositAmount($initial_deposit_amount)
    {
        $this->initial_deposit_amount = $initial_deposit_amount;
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
    
    public function getMinimumBalance()
    {
        return $this->minimum_balance;
    }
    public function setMinimumBalance($minimum_balance)
    {
        $this->minimum_balance = $minimum_balance;
        return $this;
    }
    
    public function getChequeBookFees()
    {
        return $this->cheque_book_fees;
    }
    public function setChequeBookFees($cheque_book_fees)
    {
        $this->cheque_book_fees = $cheque_book_fees;
        return $this;
    }
    
    public function getInternetBankingFees()
    {
        return $this->internet_banking_fees;
    }
    public function setInternetBankingFees($internet_banking_fees)
    {
        $this->internet_banking_fees = $internet_banking_fees;
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
    
    public function getServiceFee()
    {
        return $this->service_fee;
    }
    public function setServiceFee($service_fee)
    {
        $this->service_fee = $service_fee;
        return $this;
    }
    
    public function getHighlight()
    {
        return $this->highlight;
    }
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
        return $this;
    }
    
    public function getCitizenship()
    {
        return $this->citizenship;
    }
    public function setCitizenship($citizenship)
    {
        $this->citizenship = $citizenship;
        return $this;
    }
    
    public function getAge()
    {
        return $this->age;
    }
    public function setAge($age)
    {
        $this->age = $age;
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
    
    public function getTenor()
    {
        return $this->tenor;
    }
    public function setTenor($tenor)
    {
        $this->tenor = $tenor;
        return $this;
    }
    
    public function getLink()
    {
        return $this->link;
    }
    public function setLink($link)
    {
        $this->link = $link;
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
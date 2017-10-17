<?php
namespace Application\Entity;

class PropertyLoanPackage implements PropertyLoanPackageInterface
{
	protected $id;
    protected $title;
    protected $bank_id;
    protected $category_id;
    protected $promotions;
    protected $min_loan_amount;
    protected $property;
    protected $type;
    protected $property_status;
    protected $package;
    protected $floating_type;
    protected $sibor;
    protected $variable;
    protected $sor;
    protected $lock_in_year;
    protected $legal_subsidy;
    protected $legal_fee_subsidy;
    protected $valuation_subsidy;
    protected $fire_insurance_subsidy;
    protected $subsidy_comment;
    protected $clawback;
    protected $valuation_fee;
    protected $late_payment_fee;
    protected $early_repayment_fee;
    protected $cancellation_fee;
    protected $preferred_fire;
    protected $admin_fee;
    protected $int_year_1;
    protected $remark_year_1;
    protected $int_year_2;
    protected $remark_year_2;
    protected $int_year_3;
    protected $remark_year_3;
    protected $int_year_4;
    protected $remark_year_4;
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
    
    public function getCategoryId()
    {
        return $this->category_id;
    }
    public function setCategoryId($category_id)
    {
        $this->category_id = (int) $category_id;
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
    
    public function getMinLoanAmount()
    {
        return $this->min_loan_amount;
    }
    public function setMinLoanAmount($min_loan_amount)
    {
        $this->min_loan_amount = (int) $min_loan_amount;
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
    
    public function getRemarkYear1()
    {
        return $this->remark_year_1;
    }
    public function setRemarkYear1($remark_year_1)
    {
        $this->remark_year_1 = $remark_year_1;
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
    
    public function getRemarkYear2()
    {
        return $this->remark_year_2;
    }
    public function setRemarkYear2($remark_year_2)
    {
        $this->remark_year_2 = $remark_year_2;
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
    
    public function getRemarkYear3()
    {
        return $this->remark_year_3;
    }
    public function setRemarkYear3($remark_year_3)
    {
        $this->remark_year_3 = $remark_year_3;
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
    
    public function getRemarkYear4()
    {
        return $this->remark_year_4;
    }
    public function setRemarkYear4($remark_year_4)
    {
        $this->remark_year_4 = $remark_year_4;
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
    
    public function getLegalSubsidy()
    {
        return $this->legal_subsidy;
    }
    public function setLegalSubsidy($legal_subsidy)
    {
        $this->legal_subsidy = $legal_subsidy;
        return $this;
    }
    
    public function getLegalFeeSubsidy()
    {
        return $this->legal_fee_subsidy;
    }
    public function setLegalFeeSubsidy($legal_fee_subsidy)
    {
        $this->legal_fee_subsidy = $legal_fee_subsidy;
        return $this;
    }
    
    public function getValuationSubsidy()
    {
        return $this->valuation_subsidy;
    }
    public function setValuationSubsidy($valuation_subsidy)
    {
        $this->valuation_subsidy = $valuation_subsidy;
        return $this;
    }
    
    public function getFireInsuranceSubsidy()
    {
        return $this->fire_insurance_subsidy;
    }
    public function setFireInsuranceSubsidy($fire_insurance_subsidy)
    {
        $this->fire_insurance_subsidy = $fire_insurance_subsidy;
        return $this;
    }
    
    public function getSubsidyComment()
    {
        return $this->subsidy_comment;
    }
    public function setSubsidyComment($subsidy_comment)
    {
        $this->subsidy_comment = $subsidy_comment;
        return $this;
    }
    
    public function getClawback()
    {
        return $this->clawback;
    }
    public function setClawback($clawback)
    {
        $this->clawback = $clawback;
        return $this;
    }
    
    public function getValuationFee()
    {
        return $this->valuation_fee;
    }
    public function setValuationFee($valuation_fee)
    {
        $this->valuation_fee = $valuation_fee;
        return $this;
    }
    
    public function getLatePaymentFee()
    {
        return $this->late_payment_fee;
    }
    public function setLatePaymentFee($late_payment_fee)
    {
        $this->late_payment_fee = $late_payment_fee;
        return $this;
    }
    
    public function getEarlyRepaymentFee()
    {
        return $this->early_repayment_fee;
    }
    public function setEarlyRepaymentFee($early_repayment_fee)
    {
        $this->early_repayment_fee = $early_repayment_fee;
        return $this;
    }
    
    public function getCancellationFee()
    {
        return $this->cancellation_fee;
    }
    public function setCancellationFee($cancellation_fee)
    {
        $this->cancellation_fee = $cancellation_fee;
        return $this;
    }
    
    public function getPreferredFire()
    {
        return $this->preferred_fire;
    }
    public function setPreferredFire($preferred_fire)
    {
        $this->preferred_fire = $preferred_fire;
        return $this;
    }
    
    public function getAdminFee()
    {
        return $this->admin_fee;
    }
    public function setAdminFee($admin_fee)
    {
        $this->admin_fee = $admin_fee;
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
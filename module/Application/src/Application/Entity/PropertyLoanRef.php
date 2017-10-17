<?php
namespace Application\Entity;

class PropertyLoanRef implements PropertyLoanRefInterface
{
	protected $id;
    protected $property_loan_id;
    protected $property_package_id;
    
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }
    
    public function getPropertyLoanId()
    {
        return $this->property_loan_id;
    }
    public function setPropertyLoanId($property_loan_id)
    {
        $this->property_loan_id = (int) $property_loan_id;
        return $this;
    }
    
    public function getPropertyPackageId()
    {
        return $this->property_package_id;
    }
    public function setPropertyPackageId($property_package_id)
    {
        $this->property_package_id = (int) $property_package_id;
        return $this;
    }
    
}
<?php
namespace Application\Entity;

class Payment implements PaymentInterface
{
    protected $id;
    protected $user_id;
    protected $business_loan_id;
    protected $date_added;
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

    public function getRef()
    {
        return $this->ref;
    }
    public function setRef($ref)
    {
        $this->ref = $ref;
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
}

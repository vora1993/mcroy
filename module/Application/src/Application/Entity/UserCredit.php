<?php
namespace Application\Entity;

class UserCredit implements UserCreditInterface
{
    protected $id;
    protected $user_id;
    protected $ref_id;
    protected $credit;
    protected $date_added;
    
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
    
    public function getRefId()
    {
        return $this->ref_id;
    }
    public function setRefId($ref_id)
    {
        $this->ref_id = (int) $ref_id;
        return $this;
    }

    public function getCredit()
    {
        return $this->credit;
    }
    public function setCredit($credit)
    {
        $this->credit = (int) $credit;
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

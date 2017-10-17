<?php
namespace Application\Entity;

class Referral implements ReferralInterface
{
    protected $id;
    protected $user_id;
    protected $type;
    protected $application;
    protected $credit;
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

    public function getApplication()
    {
        return $this->application;
    }
    public function setApplication($application)
    {
        $this->application = $application;
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
        $this->status = $status;
        return $this;
    }
}

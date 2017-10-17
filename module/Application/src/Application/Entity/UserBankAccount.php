<?php
namespace Application\Entity;

class UserBankAccount implements UserBankAccountInterface
{
	protected $id;
    protected $name;
    protected $user_id;
    protected $bank;
    protected $acct_no;
    protected $date_added;
    protected $date_modified;
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
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
    
    public function getUserId()
    {
        return $this->user_id;
    }
    public function setUserId($user_id)
    {
        $this->user_id = (int) $user_id;
        return $this;
    }
    
    public function getBank()
    {
        return $this->bank;
    }
    public function setBank($bank)
    {
        $this->bank = $bank;
        return $this;
    }
    
    public function getAcctNo()
    {
        return $this->acct_no;
    }
    public function setAcctNo($acct_no)
    {
        $this->acct_no = $acct_no;
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
    
}
<?php
namespace Application\Entity;

class Role implements RoleInterface
{
	protected $id;
    protected $name;
    protected $key;
    protected $child;
    protected $is_default;
    protected $allow;
    protected $deny;
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
    
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getKey()
    {
        return $this->key;
    }
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
    
    public function getChild()
    {
        return $this->child;
    }
    public function setChild($child)
    {
        $this->child = (int) $child;
        return $this;
    }
    
    public function getIsDefault()
    {
        return $this->is_default;
    }
    public function setIsDefault($is_default)
    {
        $this->is_default = (int) $is_default;
        return $this;
    }
    
    public function getAllow()
    {
        return $this->allow;
    }
    public function setAllow($allow)
    {
        $this->allow = $allow;
        return $this;
    }
    
    public function getDeny()
    {
        return $this->deny;
    }
    public function setDeny($deny)
    {
        $this->deny = $deny;
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
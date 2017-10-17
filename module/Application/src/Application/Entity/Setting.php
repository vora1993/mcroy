<?php
namespace Application\Entity;

class Setting implements SettingInterface
{
	protected $id;
    protected $name;
    protected $key;
    protected $value;
    protected $remark;
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
    
    public function getKey()
    {
        return $this->key;
    }
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
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
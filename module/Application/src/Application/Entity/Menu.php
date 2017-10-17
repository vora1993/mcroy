<?php
namespace Application\Entity;

class Menu implements MenuInterface
{
	protected $id;
    protected $group_id;
    protected $title;
    protected $parent;
    protected $name;
    protected $route;
    protected $action;
    protected $value;
    protected $sort_order;
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
    
    public function getGroupId()
    {
        return $this->group_id;
    }
    public function setGroupId($group_id)
    {
        $this->group_id = (int) $group_id;
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
    
    public function getParent()
    {
        return $this->parent;
    }
    public function setParent($parent)
    {
        $this->parent = (int) $parent;
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
    
    public function getRoute()
    {
        return $this->route;
    }
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }
    
    public function getAction()
    {
        return $this->action;
    }
    public function setAction($action)
    {
        $this->action = $action;
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
    
    public function getSortOrder()
    {
        return $this->sort_order;
    }
    public function setSortOrder($sort_order)
    {
        $this->sort_order = (int) $sort_order;
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